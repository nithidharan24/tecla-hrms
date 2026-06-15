<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $month;
    protected $year;
    protected $departmentId;
    protected $daysInMonth;
    protected $holidays;
    protected $employeeLeaves;
    protected $schedulesByEmployee;

    public function __construct($month, $year, $departmentId = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->departmentId = $departmentId;
        $this->daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $monthEnd = (clone $monthStart)->endOfMonth()->endOfDay();
        
        // Get holidays for the month
        $this->holidays = DB::table('holidays')
            ->whereMonth('holidaydate', $month)
            ->whereYear('holidaydate', $year)
            ->get()
            ->keyBy(function ($item) {
                return date('d', strtotime($item->holidaydate));
            });

        // Get employee leaves for the month
        $this->employeeLeaves = DB::table('employee_leaves')
            ->where('status', 'approved')
            ->where(function($query) use ($monthStart, $monthEnd) {
                $query->whereDate('from_date', '<=', $monthEnd->toDateString())
                      ->whereDate('to_date', '>=', $monthStart->toDateString());
            })
            ->get()
            ->groupBy('employee_id');

        // Get ALL schedules for the selected month
        $this->schedulesByEmployee = DB::table('schedule')
            ->join('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->where('schedule.deleted_at', 0)
            ->where(function($query) use ($monthStart, $monthEnd) {
                // Include schedules that overlap with the selected month
                $query->where(function($q) use ($monthStart, $monthEnd) {
                    $q->whereDate('schedule.schedule_end_date', '>=', $monthStart->toDateString())
                      ->whereDate('schedule.schedule_start_date', '<=', $monthEnd->toDateString());
                })->orWhere(function($q) use ($monthEnd) {
                    // Include weekly repeating schedules that started before month end
                    $q->where('schedule.repeat_every_week', '>', 0)
                      ->whereDate('schedule.schedule_start_date', '<=', $monthEnd->toDateString());
                });
            })
            ->select(
                'schedule.id',
                'schedule.employee_id',
                'schedule.schedule_start_date',
                'schedule.schedule_end_date',
                'schedule.repeat_every_week',
                'schedule.shift_id',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.days_of_week'
            )
            ->orderBy('schedule.schedule_start_date')
            ->get()
            ->groupBy('employee_id');
    }

    public function collection()
    {
        // Get employees with their basic info
        $employeesQuery = DB::table('allemployees')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->where('allemployees.deleted_at', 0)
            ->where('allemployees.status', 'active')
            ->select(
                'allemployees.*',
                'department.department'
            );

        if ($this->departmentId) {
            $employeesQuery->where('allemployees.department', $this->departmentId);
        }

        $employees = $employeesQuery->orderBy('allemployees.firstname')->get();

        // Get attendance data for all employees
        $attendanceData = [];
        $employeeIds = $employees->pluck('id')->toArray();
        
        if (!empty($employeeIds)) {
            $attendances = DB::table('attendances')
                ->whereIn('employee_id', $employeeIds)
                ->whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->get()
                ->groupBy('employee_id');

            foreach ($attendances as $empId => $empAttendances) {
                $attendanceData[$empId] = $empAttendances->keyBy(function ($item) {
                    return date('d', strtotime($item->date));
                });
            }
        }

        // Initialize attendance data for all employees
        foreach ($employeeIds as $empId) {
            if (!isset($attendanceData[$empId])) {
                $attendanceData[$empId] = collect();
            }
        }

        // Prepare data for export
        $exportData = collect();
        
        foreach ($employees as $employee) {
            $row = (object)[
                'employee' => $employee,
                'attendanceData' => $attendanceData[$employee->id] ?? collect(),
                'employeeLeaves' => $this->employeeLeaves[$employee->id] ?? collect(),
                'employeeSchedules' => $this->schedulesByEmployee[$employee->id] ?? collect()
            ];
            $exportData->push($row);
        }

        return $exportData;
    }

    public function headings(): array
    {
        $headings = [
            'Employee Name',
            'Employee ID',
            'Department',
            'Shift',
        ];

        // Add day headings
        for ($day = 1; $day <= $this->daysInMonth; $day++) {
            $currentDate = sprintf('%04d-%02d-%02d', $this->year, $this->month, $day);
            $dayName = date('D', strtotime($currentDate));
            $headings[] = "{$day} ({$dayName})";
        }

        // Add summary columns
        $headings = array_merge($headings, [
            'Present Days',
            'Absent Days',
            'Late Days',
            'Leave Days',
            'Total Hours',
            'Attendance %'
        ]);

        return $headings;
    }

    /**
     * Find active schedule for a specific date
     */
    private function findScheduleForDate($employeeSchedules, string $currentDate)
    {
        if (!$employeeSchedules || $employeeSchedules->isEmpty()) {
            return null;
        }
        
        foreach ($employeeSchedules as $sch) {
            // For weekly repeating schedules
            if ($sch->repeat_every_week) {
                if ($currentDate >= $sch->schedule_start_date) {
                    return $sch;
                }
            } 
            // For regular schedules
            else {
                if ($currentDate >= $sch->schedule_start_date && $currentDate <= $sch->schedule_end_date) {
                    return $sch;
                }
            }
        }
        return null;
    }

    /**
     * Check if a date is a working weekday based on schedule days
     */
    private function isWorkingWeekday(string $currentDate, ?string $daysOfWeek): bool
    {
        if (!$daysOfWeek || trim($daysOfWeek) === '') {
            return false;
        }
        
        $tokens = array_map(function ($v) {
            return strtolower(trim($v));
        }, explode(',', $daysOfWeek));

        $dayFull = strtolower(date('l', strtotime($currentDate))); // monday
        $dayShort = strtolower(date('D', strtotime($currentDate))); // mon
        $dayNum = (string) date('N', strtotime($currentDate)); // 1-7 (Monday=1)
        
        // Handle Sunday representation
        $isSunday = $dayNum === '7';
        $dayNumSundayZero = $dayNum === '7' ? '0' : $dayNum;

        return in_array($dayFull, $tokens, true)
            || in_array($dayShort, $tokens, true)
            || in_array($dayNum, $tokens, true)
            || ($isSunday && in_array('0', $tokens, true))
            || in_array($dayNumSundayZero, $tokens, true);
    }

    public function map($row): array
    {
        $employee = $row->employee;
        $attendanceData = $row->attendanceData;
        $employeeLeaves = $row->employeeLeaves;
        $employeeSchedules = $row->employeeSchedules;

        $mappedRow = [
            $employee->firstname . ' ' . $employee->lastname,
            $employee->employeeid ?? 'N/A',
            $employee->department ?? 'N/A',
            $employee->shift_name ?? 'N/A',
        ];

        $presentCount = 0;
        $lateCount = 0;
        $leaveCount = 0;
        $totalHours = 0;
        $workingDays = 0;

        // Add attendance status for each day
        for ($day = 1; $day <= $this->daysInMonth; $day++) {
            $currentDate = sprintf('%04d-%02d-%02d', $this->year, $this->month, $day);
            $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
            $attendance = $attendanceData[$dayKey] ?? null;
            
            // Check if it's a company holiday
            $isCompanyHoliday = isset($this->holidays[$dayKey]);
            
            // Check if employee is on leave
            $isEmployeeOnLeave = false;
            foreach ($employeeLeaves as $leave) {
                if ($currentDate >= $leave->from_date && $currentDate <= $leave->to_date) {
                    $isEmployeeOnLeave = true;
                    $leaveCount++;
                    break;
                }
            }

            // Find active schedule for this date
            $activeSchedule = $this->findScheduleForDate($employeeSchedules, $currentDate);
            
            // Check if employee should work today
            $shouldWork = false;
            if (!$isCompanyHoliday && !$isEmployeeOnLeave && $activeSchedule) {
                $shouldWork = $this->isWorkingWeekday($currentDate, $activeSchedule->days_of_week);
            }

            if ($shouldWork) {
                $workingDays++;
            }

            // Determine status for this day
            if ($isCompanyHoliday) {
                $status = 'Holiday';
            } elseif ($isEmployeeOnLeave) {
                $status = 'Leave';
            } elseif (!$shouldWork) {
                $status = 'Weekend';
            } elseif ($attendance) {
                if ($attendance->punch_in && $attendance->punch_out) {
                    $status = 'Present';
                    $presentCount++;
                    $totalHours += $attendance->working_hours ?? 0;
                    if ($attendance->status == 'late') {
                        $status = 'Late';
                        $lateCount++;
                    }
                } elseif ($attendance->punch_in) {
                    $status = 'Partial';
                } else {
                    $status = 'Absent';
                }
            } else {
                $status = 'Absent';
            }

            $mappedRow[] = $status;
        }

        // Add summary data
        $absentDays = $workingDays - $presentCount;
        $attendancePercentage = $workingDays > 0 ? round(($presentCount / $workingDays) * 100, 2) : 0;

        $mappedRow = array_merge($mappedRow, [
            $presentCount,
            $absentDays,
            $lateCount,
            $leaveCount,
            number_format($totalHours, 2),
            $attendancePercentage . '%'
        ]);

        return $mappedRow;
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);

        // Add borders to all cells
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Center align attendance status columns
        $startCol = 5; // After employee info columns
        $endCol = 4 + $this->daysInMonth; // Days columns
        
        for ($col = $startCol; $col <= $endCol; $col++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getStyle("{$columnLetter}2:{$columnLetter}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Apply conditional formatting for different statuses
        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = $startCol; $col <= $endCol; $col++) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $cellValue = $sheet->getCell($columnLetter . $row)->getValue();
                
                switch ($cellValue) {
                    case 'Present':
                        $sheet->getStyle($columnLetter . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('C6EFCE');
                        break;
                    case 'Late':
                        $sheet->getStyle($columnLetter . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('FFEB9C');
                        break;
                    case 'Absent':
                        $sheet->getStyle($columnLetter . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('FFC7CE');
                        break;
                    case 'Leave':
                        $sheet->getStyle($columnLetter . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F4B084');
                        break;
                    case 'Holiday':
                        $sheet->getStyle($columnLetter . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('E0B0FF');
                        break;
                    case 'Weekend':
                        $sheet->getStyle($columnLetter . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F2F2F2');
                        break;
                }
            }
        }

        return [];
    }

    public function title(): string
    {
        $monthName = date('F', mktime(0, 0, 0, $this->month, 1));
        return "Attendance {$monthName} {$this->year}";
    }
}