<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeReportController extends Controller
{
    /**
     * Branch access logic:
     * - Admin: see ALL branches (no filter)
     * - Employee: only their own branch (filter by session branch_id)
     * - Others/Super Admin: see ALL branches (no filter)
     */
    private function getBranchFilter()
    {
        $role = Session::get('role');       // e.g., 'admin', 'employee', 'super-admin'
        $branchId = Session::get('branch_id');

        if ($role === 'employee' && $branchId) {
            return (int) $branchId;
        }

        // Admin and Super-Admin (or other roles) => no filter
        return null;
    }

    private function role(): ?string
    {
        return Session::get('role');
    }

    /**
     * View for Employee Reports (Directory + Attendance)
     */
    public function index()
    {
        $departments = DB::table('department')->get();
        $designations = DB::table('designation')->get();

        $role = $this->role();
        $branchId = Session::get('branch_id');
        if ($role === 'employee' && $branchId) {
            // Employees: only their branch in the dropdown (read-only in practice)
            $branches = DB::table('branches')->where('status', 1)->where('id', $branchId)->get();
        } else {
            // Admins and others: all branches
            $branches = DB::table('branches')->where('status', 1)->select('id', 'name')->get();
        }

        return view('hrms.hr.Reports.Employee.employee-report', compact('departments', 'designations', 'branches'));
    }

    /**
     * Employee Directory - DataTables JSON
     */
    public function getEmployeeDirectoryReport(Request $request)
    {
        try {
            $branchFilter = $this->getBranchFilter();

            $query = DB::table('allemployees')
                ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
                ->leftJoin('department', 'allemployees.department', '=', 'department.id')
                ->leftJoin('hierarchies', 'allemployees.hierarchy_id', '=', 'hierarchies.id')
                ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
                ->leftJoin('allemployees as manager', 'allemployees.manager_id', '=', 'manager.id')
                ->where('allemployees.deleted_at', 0)
                ->select([
                    'allemployees.id',
                    'allemployees.employeeid',
                    'allemployees.firstname',
                    'allemployees.lastname',
                    'allemployees.email',
                    'allemployees.phone',
                    'allemployees.joiningdate',
                    'allemployees.branch_id',
                    'branches.name as branch_name',
                    'department.department as department_name',
                    'designation.designation as designation_name',
                    DB::raw('CONCAT(manager.firstname, " ", manager.lastname) as manager_name'),
                    'hierarchies.hierarchy_level',
                ]);

            // Enforce branch restriction for employees
            if ($branchFilter) {
                $query->where('allemployees.branch_id', $branchFilter);
            }

            // Filters
            if ($request->filled('employee_id')) {
                $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
            }
            if ($request->filled('employee_name')) {
                $name = $request->employee_name;
                $query->where(function ($q) use ($name) {
                    $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                      ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
                });
            }
            if ($request->filled('department_id')) {
                $query->where('allemployees.department', $request->department_id);
            }
            // Only honor branch_id filter if no enforced branchFilter
            if (!$branchFilter && $request->filled('branch_id')) {
                $query->where('allemployees.branch_id', $request->branch_id);
            }
            if ($request->filled('designation_id')) {
                $query->where('allemployees.designation', $request->designation_id);
            }
            if ($request->filled('joining_from')) {
                $query->whereDate('allemployees.joiningdate', '>=', $request->joining_from);
            }
            if ($request->filled('joining_to')) {
                $query->whereDate('allemployees.joiningdate', '<=', $request->joining_to);
            }

            $totalRecords = (clone $query)->count();

            // Ordering
            $orderColumn = null;
            $orderDir = 'desc';
            if ($request->has('order')) {
                $dir = $request->order[0]['dir'] ?? 'desc';
                $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
                $map = [
                    'employeeid' => 'allemployees.employeeid',
                    'full_name' => null, // special sorting
                    'email' => 'allemployees.email',
                    'phone' => 'allemployees.phone',
                    'department_name' => 'department.department',
                    'designation_name' => 'designation.designation',
                    'branch_name' => 'branches.name',
                    'joiningdate' => 'allemployees.joiningdate',
                    'manager_name' => DB::raw('CONCAT(manager.firstname, " ", manager.lastname)'),
                    'hierarchy_level' => 'hierarchies.hierarchy_level',
                ];
                if (array_key_exists($colKey, $map)) {
                    $orderColumn = $map[$colKey];
                    $orderDir = in_array(strtolower($dir), ['asc', 'desc']) ? $dir : 'desc';
                }
            }

            if ($orderColumn === null && isset($colKey) && $colKey === 'full_name') {
                $query->orderBy('allemployees.firstname', $orderDir)
                      ->orderBy('allemployees.lastname', $orderDir);
            } elseif ($orderColumn) {
                $query->orderBy($orderColumn, $orderDir);
            } else {
                $query->orderBy('allemployees.joiningdate', 'desc');
            }

            // Pagination
            if ($request->has('start') && $request->has('length')) {
                $query->offset((int) $request->start)->limit((int) $request->length);
            }

            $employees = $query->get()->map(function ($e) {
                $e->full_name = trim(($e->firstname ?? '') . ' ' . ($e->lastname ?? ''));
                return $e;
            });

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $employees,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getEmployeeDirectoryReport: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Employee Directory - CSV
     */
    public function exportEmployeeDirectory(Request $request)
    {
        try {
            $branchFilter = $this->getBranchFilter();

            $query = DB::table('allemployees')
                ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
                ->leftJoin('department', 'allemployees.department', '=', 'department.id')
                ->leftJoin('hierarchies', 'allemployees.hierarchy_id', '=', 'hierarchies.id')
                ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
                ->leftJoin('allemployees as manager', 'allemployees.manager_id', '=', 'manager.id')
                ->where('allemployees.deleted_at', 0)
                ->select([
                    'allemployees.employeeid',
                    'allemployees.firstname',
                    'allemployees.lastname',
                    'allemployees.email',
                    'allemployees.phone',
                    'allemployees.joiningdate',
                    'department.department as department_name',
                    'designation.designation as designation_name',
                    'branches.name as branch_name',
                    DB::raw('CONCAT(manager.firstname, " ", manager.lastname) as manager_name'),
                    'hierarchies.hierarchy_level',
                ]);

            if ($branchFilter) {
                $query->where('allemployees.branch_id', $branchFilter);
            }
            if ($request->filled('employee_id')) {
                $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
            }
            if ($request->filled('employee_name')) {
                $name = $request->employee_name;
                $query->where(function ($q) use ($name) {
                    $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                      ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
                });
            }
            if ($request->filled('department_id')) {
                $query->where('allemployees.department', $request->department_id);
            }
            if (!$branchFilter && $request->filled('branch_id')) {
                $query->where('allemployees.branch_id', $request->branch_id);
            }
            if ($request->filled('designation_id')) {
                $query->where('allemployees.designation', $request->designation_id);
            }
            if ($request->filled('joining_from')) {
                $query->whereDate('allemployees.joiningdate', '>=', $request->joining_from);
            }
            if ($request->filled('joining_to')) {
                $query->whereDate('allemployees.joiningdate', '<=', $request->joining_to);
            }

            $rows = $query->get();

            $filename = 'employee_directory_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function () use ($rows) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF"); // BOM

                fputcsv($file, [
                    'Employee ID',
                    'Full Name',
                    'Email',
                    'Phone',
                    'Joining Date',
                    'Department',
                    'Designation',
                    'Branch',
                    'Manager',
                    'Hierarchy Level'
                ]);

                foreach ($rows as $r) {
                    fputcsv($file, [
                        $r->employeeid,
                        trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? '')),
                        $r->email,
                        $r->phone,
                        $r->joiningdate ? date('d/m/Y', strtotime($r->joiningdate)) : '',
                        $r->department_name,
                        $r->designation_name,
                        $r->branch_name,
                        $r->manager_name,
                        $r->hierarchy_level,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in exportEmployeeDirectory: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    /**
     * Employee Directory - PDF
     */
    public function exportEmployeeDirectoryPdf(Request $request)
    {
        $branchFilter = $this->getBranchFilter();

        $query = DB::table('allemployees')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('allemployees as manager', 'allemployees.manager_id', '=', 'manager.id')
            ->where('allemployees.deleted_at', 0)
            ->select([
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.email',
                'allemployees.phone',
                'allemployees.joiningdate',
                'department.department as department_name',
                'designation.designation as designation_name',
                'branches.name as branch_name',
                DB::raw('CONCAT(manager.firstname, " ", manager.lastname) as manager_name'),
            ]);

        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
        }
        if ($request->filled('employee_name')) {
            $name = $request->employee_name;
            $query->where(function ($q) use ($name) {
                $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('department_id')) {
            $query->where('allemployees.department', $request->department_id);
        }
        if (!$branchFilter && $request->filled('branch_id')) {
            $query->where('allemployees.branch_id', $request->branch_id);
        }
        if ($request->filled('designation_id')) {
            $query->where('allemployees.designation', $request->designation_id);
        }
        if ($request->filled('joining_from')) {
            $query->whereDate('allemployees.joiningdate', '>=', $request->joining_from);
        }
        if ($request->filled('joining_to')) {
            $query->whereDate('allemployees.joiningdate', '<=', $request->joining_to);
        }

        $rows = $query->orderBy('allemployees.joiningdate', 'desc')->get();

        $pdf = Pdf::loadView('hrms.hr.Reports.Employee.pdf-employee-directory', [
            'rows' => $rows,
            'generated_at' => now()->format('d/m/Y H:i'),
            'title' => 'Employee Directory Report'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('employee_directory_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Attendance - DataTables JSON
     */
    public function getAttendanceReport(Request $request)
    {
        try {
            $branchFilter = $this->getBranchFilter();

            $query = DB::table('attendances')
                ->join('allemployees', 'attendances.employee_id', '=', 'allemployees.id')
                ->leftJoin('department', 'allemployees.department', '=', 'department.id')
                ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
                ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
                ->select([
                    'attendances.id',
                    'attendances.date',
                    'attendances.punch_in',
                    'attendances.punch_out',
                    'attendances.working_hours',
                    'attendances.actual_working_minutes',
                    'attendances.total_break_taken',
                    'attendances.overtime_hours',
                    'attendances.overtime_minutes',
                    'attendances.undertime_minutes',
                    'attendances.status',
                    'allemployees.employeeid',
                    'allemployees.firstname',
                    'allemployees.lastname',
                    'department.department as department_name',
                    'branches.name as branch_name',
                    'shifts.shift_name'
                ]);

            if ($branchFilter) {
                $query->where('allemployees.branch_id', $branchFilter);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('attendances.date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('attendances.date', '<=', $request->date_to);
            }
            if ($request->filled('employee_id')) {
                $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
            }
            if ($request->filled('employee_name')) {
                $name = $request->employee_name;
                $query->where(function ($q) use ($name) {
                    $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                      ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
                });
            }
            if ($request->filled('department_id')) {
                $query->where('allemployees.department', $request->department_id);
            }
            if (!$branchFilter && $request->filled('branch_id')) {
                $query->where('allemployees.branch_id', $request->branch_id);
            }
            if ($request->filled('status')) {
                $query->where('attendances.status', $request->status);
            }
            if ($request->filled('min_work_hours')) {
                $query->where('attendances.working_hours', '>=', (float) $request->min_work_hours);
            }
            if ((int) $request->overtime_only === 1) {
                $query->where(function ($q) {
                    $q->where('attendances.overtime_hours', '>', 0)
                      ->orWhere('attendances.overtime_minutes', '>', 0);
                });
            }

            $totalRecords = (clone $query)->count();

            $orderColumn = null;
            $orderDir = 'desc';
            if ($request->has('order')) {
                $dir = $request->order[0]['dir'] ?? 'desc';
                $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;

                $map = [
                    'date' => 'attendances.date',
                    'employeeid' => 'allemployees.employeeid',
                    'full_name' => null,
                    'department_name' => 'department.department',
                    'branch_name' => 'branches.name',
                    'shift_name' => 'shifts.shift_name',
                    'punch_in' => 'attendances.punch_in',
                    'punch_out' => 'attendances.punch_out',
                    'working_hours' => 'attendances.working_hours',
                    'status' => 'attendances.status',
                ];

                if (array_key_exists($colKey, $map)) {
                    $orderColumn = $map[$colKey];
                    $orderDir = in_array(strtolower($dir), ['asc','desc']) ? $dir : 'desc';
                }
            }

            if ($orderColumn === null && isset($colKey) && $colKey === 'full_name') {
                $query->orderBy('allemployees.firstname', $orderDir)
                      ->orderBy('allemployees.lastname', $orderDir);
            } elseif ($orderColumn) {
                $query->orderBy($orderColumn, $orderDir);
            } else {
                $query->orderBy('attendances.date', 'desc')->orderBy('attendances.punch_in', 'asc');
            }

            if ($request->has('start') && $request->has('length')) {
                $query->offset((int) $request->start)->limit((int) $request->length);
            }

            $rows = $query->get()->map(function ($r) {
                $r->full_name = trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? ''));
                return $r;
            });

            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getAttendanceReport: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Attendance - CSV
     */
    public function exportAttendance(Request $request)
    {
        try {
            $branchFilter = $this->getBranchFilter();

            $query = DB::table('attendances')
                ->join('allemployees', 'attendances.employee_id', '=', 'allemployees.id')
                ->leftJoin('department', 'allemployees.department', '=', 'department.id')
                ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
                ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
                ->select([
                    'attendances.date',
                    'attendances.punch_in',
                    'attendances.punch_out',
                    'attendances.working_hours',
                    'attendances.actual_working_minutes',
                    'attendances.total_break_taken',
                    'attendances.overtime_hours',
                    'attendances.overtime_minutes',
                    'attendances.undertime_minutes',
                    'attendances.status',
                    'allemployees.employeeid',
                    'allemployees.firstname',
                    'allemployees.lastname',
                    'department.department as department_name',
                    'branches.name as branch_name',
                    'shifts.shift_name'
                ]);

            if ($branchFilter) {
                $query->where('allemployees.branch_id', $branchFilter);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('attendances.date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('attendances.date', '<=', $request->date_to);
            }
            if ($request->filled('employee_id')) {
                $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
            }
            if ($request->filled('employee_name')) {
                $name = $request->employee_name;
                $query->where(function ($q) use ($name) {
                    $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                      ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
                });
            }
            if ($request->filled('department_id')) {
                $query->where('allemployees.department', $request->department_id);
            }
            if (!$branchFilter && $request->filled('branch_id')) {
                $query->where('allemployees.branch_id', $request->branch_id);
            }
            if ($request->filled('status')) {
                $query->where('attendances.status', $request->status);
            }
            if ($request->filled('min_work_hours')) {
                $query->where('attendances.working_hours', '>=', (float) $request->min_work_hours);
            }
            if ((int) $request->overtime_only === 1) {
                $query->where(function ($q) {
                    $q->where('attendances.overtime_hours', '>', 0)
                      ->orWhere('attendances.overtime_minutes', '>', 0);
                });
            }

            $rows = $query->orderBy('attendances.date', 'desc')->get();

            $filename = 'attendance_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function () use ($rows) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");

                fputcsv($file, [
                    'Date',
                    'Employee ID',
                    'Employee Name',
                    'Department',
                    'Branch',
                    'Shift',
                    'Punch In',
                    'Punch Out',
                    'Working Hours',
                    'Actual Working Minutes',
                    'Total Break (min)',
                    'Overtime Hours',
                    'Overtime Minutes',
                    'Undertime Minutes',
                    'Status'
                ]);

                foreach ($rows as $r) {
                    fputcsv($file, [
                        $r->date ? date('d/m/Y', strtotime($r->date)) : '',
                        $r->employeeid,
                        trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? '')),
                        $r->department_name,
                        $r->branch_name,
                        $r->shift_name,
                        $r->punch_in ? date('d/m/Y H:i', strtotime($r->punch_in)) : '',
                        $r->punch_out ? date('d/m/Y H:i', strtotime($r->punch_out)) : '',
                        is_null($r->working_hours) ? '' : number_format((float) $r->working_hours, 2),
                        $r->actual_working_minutes,
                        $r->total_break_taken,
                        $r->overtime_hours,
                        $r->overtime_minutes,
                        $r->undertime_minutes,
                        $r->status
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Error in exportAttendance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    /**
     * Attendance - PDF
     */
    public function exportAttendancePdf(Request $request)
    {
        $branchFilter = $this->getBranchFilter();

        $query = DB::table('attendances')
            ->join('allemployees', 'attendances.employee_id', '=', 'allemployees.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->select([
                'attendances.date',
                'attendances.punch_in',
                'attendances.punch_out',
                'attendances.working_hours',
                'attendances.actual_working_minutes',
                'attendances.total_break_taken',
                'attendances.overtime_hours',
                'attendances.overtime_minutes',
                'attendances.undertime_minutes',
                'attendances.status',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'department.department as department_name',
                'branches.name as branch_name',
                'shifts.shift_name'
            ]);

        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('attendances.date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('attendances.date', '<=', $request->date_to);
        }
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
        }
        if ($request->filled('employee_name')) {
            $name = $request->employee_name;
            $query->where(function ($q) use ($name) {
                $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('department_id')) {
            $query->where('allemployees.department', $request->department_id);
        }
        if (!$branchFilter && $request->filled('branch_id')) {
            $query->where('allemployees.branch_id', $request->branch_id);
        }
        if ($request->filled('status')) {
            $query->where('attendances.status', $request->status);
        }
        if ($request->filled('min_work_hours')) {
            $query->where('attendances.working_hours', '>=', (float) $request->min_work_hours);
        }
        if ((int) $request->overtime_only === 1) {
            $query->where(function ($q) {
                $q->where('attendances.overtime_hours', '>', 0)
                  ->orWhere('attendances.overtime_minutes', '>', 0);
            });
        }

        $rows = $query->orderBy('attendances.date', 'desc')->get();

        $pdf = Pdf::loadView('hrms.hr.Reports.Employee.pdf-attendance', [
            'rows' => $rows,
            'generated_at' => now()->format('d/m/Y H:i'),
            'title' => 'Attendance Report'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('attendance_report_' . date('Y-m-d') . '.pdf');
    }
    /**
 * Overtime Report - DataTables JSON
 */
public function getOvertimeReport(Request $request)
{
    try {
        $branchFilter = $this->getBranchFilter();

        $query = DB::table('employee_overtime')
            ->join('allemployees', 'employee_overtime.employee_id', '=', 'allemployees.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('allemployees as approver', 'employee_overtime.approved_by', '=', 'approver.id')
            ->select([
                'employee_overtime.id',
                'employee_overtime.overtime_date',
                'employee_overtime.overtime_hours',
                'employee_overtime.overtime_rate',
                'employee_overtime.overtime_amount',
                'employee_overtime.status',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'department.department as department_name',
                'branches.name as branch_name',
                DB::raw("CONCAT(approver.firstname, ' ', approver.lastname) as approver_name")
            ])
            ->whereMonth('employee_overtime.overtime_date', $request->month)
            ->whereYear('employee_overtime.overtime_date', $request->year);

        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('employee_overtime.status', $request->status);
        }
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
        }
        if ($request->filled('employee_name')) {
            $name = $request->employee_name;
            $query->where(function ($q) use ($name) {
                $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('department_id')) {
            $query->where('allemployees.department', $request->department_id);
        }
        if (!$branchFilter && $request->filled('branch_id')) {
            $query->where('allemployees.branch_id', $request->branch_id);
        }

        $totalRecords = (clone $query)->count();

        // Ordering
        $orderColumn = null;
        $orderDir = 'desc';
        if ($request->has('order')) {
            $dir = $request->order[0]['dir'] ?? 'desc';
            $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
            $map = [
                'overtime_date' => 'employee_overtime.overtime_date',
                'employeeid' => 'allemployees.employeeid',
                'full_name' => null,
                'department_name' => 'department.department',
                'branch_name' => 'branches.name',
                'overtime_hours' => 'employee_overtime.overtime_hours',
                'overtime_rate' => 'employee_overtime.overtime_rate',
                'overtime_amount' => 'employee_overtime.overtime_amount',
                'status' => 'employee_overtime.status',
                'approver_name' => DB::raw("CONCAT(approver.firstname, ' ', approver.lastname)"),
            ];
            if (array_key_exists($colKey, $map)) {
                $orderColumn = $map[$colKey];
                $orderDir = in_array(strtolower($dir), ['asc', 'desc']) ? $dir : 'desc';
            }
        }

        if ($orderColumn === null && isset($colKey) && $colKey === 'full_name') {
            $query->orderBy('allemployees.firstname', $orderDir)
                  ->orderBy('allemployees.lastname', $orderDir);
        } elseif ($orderColumn) {
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->orderBy('employee_overtime.overtime_date', 'desc');
        }

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->offset((int) $request->start)->limit((int) $request->length);
        }

        $rows = $query->get()->map(function ($r) {
            $r->full_name = trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? ''));
            return $r;
        });

        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $rows
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in getOvertimeReport: ' . $e->getMessage());
        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Overtime Report - CSV
 */
public function exportOvertime(Request $request)
{
    try {
        $branchFilter = $this->getBranchFilter();

        $query = DB::table('employee_overtime')
            ->join('allemployees', 'employee_overtime.employee_id', '=', 'allemployees.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('allemployees as approver', 'employee_overtime.approved_by', '=', 'approver.id')
            ->select([
                'employee_overtime.overtime_date',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'department.department as department_name',
                'branches.name as branch_name',
                'employee_overtime.overtime_hours',
                'employee_overtime.overtime_rate',
                'employee_overtime.overtime_amount',
                'employee_overtime.status',
                DB::raw("CONCAT(approver.firstname, ' ', approver.lastname) as approver_name"),
                'employee_overtime.approved_at',
                'employee_overtime.remarks'
            ])
            ->whereMonth('employee_overtime.overtime_date', $request->month)
            ->whereYear('employee_overtime.overtime_date', $request->year);

        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('employee_overtime.status', $request->status);
        }
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
        }
        if ($request->filled('employee_name')) {
            $name = $request->employee_name;
            $query->where(function ($q) use ($name) {
                $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('department_id')) {
            $query->where('allemployees.department', $request->department_id);
        }
        if (!$branchFilter && $request->filled('branch_id')) {
            $query->where('allemployees.branch_id', $request->branch_id);
        }

        $rows = $query->orderBy('employee_overtime.overtime_date', 'desc')->get();

        $filename = 'overtime_report_' . $request->month . '_' . $request->year . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM

            fputcsv($file, [
                'Date',
                'Employee ID',
                'First Name',
                'Last Name',
                'Department',
                'Branch',
                'Overtime Hours',
                'Rate (₹/hr)',
                'Amount (₹)',
                'Status',
                'Approved By',
                'Approved At',
                'Remarks'
            ]);

            foreach ($rows as $r) {
                fputcsv($file, [
                    $r->overtime_date ? date('d/m/Y', strtotime($r->overtime_date)) : '',
                    $r->employeeid,
                    $r->firstname,
                    $r->lastname,
                    $r->department_name,
                    $r->branch_name,
                    $r->overtime_hours,
                    $r->overtime_rate,
                    $r->overtime_amount,
                    ucfirst($r->status),
                    $r->approver_name,
                    $r->approved_at ? date('d/m/Y H:i', strtotime($r->approved_at)) : '',
                    $r->remarks
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        \Log::error('Error in exportOvertime: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
    }
}

/**
 * Overtime Report - PDF
 */
public function exportOvertimePdf(Request $request)
{
    $branchFilter = $this->getBranchFilter();

    $query = DB::table('employee_overtime')
        ->join('allemployees', 'employee_overtime.employee_id', '=', 'allemployees.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
        ->leftJoin('allemployees as approver', 'employee_overtime.approved_by', '=', 'approver.id')
        ->select([
            'employee_overtime.overtime_date',
            'allemployees.employeeid',
            'allemployees.firstname',
            'allemployees.lastname',
            'department.department as department_name',
            'branches.name as branch_name',
            'employee_overtime.overtime_hours',
            'employee_overtime.overtime_rate',
            'employee_overtime.overtime_amount',
            'employee_overtime.status',
            DB::raw("CONCAT(approver.firstname, ' ', approver.lastname) as approver_name"),
            'employee_overtime.approved_at',
            'employee_overtime.remarks'
        ])
        ->whereMonth('employee_overtime.overtime_date', $request->month)
        ->whereYear('employee_overtime.overtime_date', $request->year);

    if ($branchFilter) {
        $query->where('allemployees.branch_id', $branchFilter);
    }
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('employee_overtime.status', $request->status);
    }
    if ($request->filled('employee_id')) {
        $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
    }
    if ($request->filled('employee_name')) {
        $name = $request->employee_name;
        $query->where(function ($q) use ($name) {
            $q->where('allemployees.firstname', 'like', '%' . $name . '%')
              ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
        });
    }
    if ($request->filled('department_id')) {
        $query->where('allemployees.department', $request->department_id);
    }
    if (!$branchFilter && $request->filled('branch_id')) {
        $query->where('allemployees.branch_id', $request->branch_id);
    }

    $rows = $query->orderBy('employee_overtime.overtime_date', 'desc')->get();

    $pdf = Pdf::loadView('hrms.hr.Reports.Employee.pdf-overtime', [
        'rows' => $rows,
        'month' => $request->month,
        'year' => $request->year,
        'generated_at' => now()->format('d/m/Y H:i'),
        'title' => 'Overtime Report - ' . date('F', mktime(0, 0, 0, $request->month, 1)) . ' ' . $request->year
    ])->setPaper('a4', 'landscape');

    return $pdf->download('overtime_report_' . $request->month . '_' . $request->year . '.pdf');
}
// In your EmployeeReportController.php

/**
 * Schedule Report - DataTables JSON
 */
public function getScheduleReport(Request $request)
{
    try {
        $branchFilter = $this->getBranchFilter();

        $query = DB::table('schedule')
            ->join('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->select([
                'schedule.id',
                'schedule.schedule_start_date',
                'schedule.schedule_end_date',
                'schedule.publish',
                'schedule.is_current',
                'schedule.created_at',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'department.department as department_name',
                'branches.name as branch_name',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.break_time',
                'shifts.days_of_week',
                'schedule.repeat_every_week'
            ])
            ->where('schedule.deleted_at', 0)
            ->where('allemployees.deleted_at', 0);

        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }

        // Filters
        if ($request->filled('date_from')) {
            $query->whereDate('schedule.schedule_start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('schedule.schedule_end_date', '<=', $request->date_to);
        }
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
        }
        if ($request->filled('employee_name')) {
            $name = $request->employee_name;
            $query->where(function ($q) use ($name) {
                $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('department_id')) {
            $query->where('schedule.department_id', $request->department_id);
        }
        if (!$branchFilter && $request->filled('branch_id')) {
            $query->where('allemployees.branch_id', $request->branch_id);
        }
        if ($request->filled('shift_id')) {
            $query->where('schedule.shift_id', $request->shift_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('schedule.is_current', 1);
            } elseif ($request->status === 'upcoming') {
                $query->where('schedule.schedule_start_date', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('schedule.schedule_end_date', '<', now());
            }
        }
        if ($request->filled('publish_status') && $request->publish_status !== 'all') {
            $query->where('schedule.publish', $request->publish_status);
        }

        $totalRecords = (clone $query)->count();

        // Ordering
        $orderColumn = null;
        $orderDir = 'desc';
        if ($request->has('order')) {
            $dir = $request->order[0]['dir'] ?? 'desc';
            $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
            $map = [
                'schedule_start_date' => 'schedule.schedule_start_date',
                'schedule_end_date' => 'schedule.schedule_end_date',
                'employeeid' => 'allemployees.employeeid',
                'full_name' => null,
                'department_name' => 'department.department',
                'branch_name' => 'branches.name',
                'shift_name' => 'shifts.shift_name',
                'start_time' => 'shifts.start_time',
                'end_time' => 'shifts.end_time',
                'status' => null, // Special case
                'publish_status' => 'schedule.publish',
                'created_at' => 'schedule.created_at'
            ];
            if (array_key_exists($colKey, $map)) {
                $orderColumn = $map[$colKey];
                $orderDir = in_array(strtolower($dir), ['asc', 'desc']) ? $dir : 'desc';
            }
        }

        if ($orderColumn === null && isset($colKey) && $colKey === 'full_name') {
            $query->orderBy('allemployees.firstname', $orderDir)
                  ->orderBy('allemployees.lastname', $orderDir);
        } elseif ($orderColumn === null && isset($colKey) && $colKey === 'status') {
            $query->orderBy('schedule.schedule_start_date', $orderDir)
                  ->orderBy('schedule.schedule_end_date', $orderDir);
        } elseif ($orderColumn) {
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->orderBy('schedule.schedule_start_date', 'desc');
        }

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->offset((int) $request->start)->limit((int) $request->length);
        }

        $rows = $query->get()->map(function ($r) {
            $r->full_name = trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? ''));
            
            // Calculate status
            $today = now();
            $startDate = Carbon::parse($r->schedule_start_date);
            $endDate = Carbon::parse($r->schedule_end_date);
            
            if ($startDate > $today) {
                $r->status = 'Upcoming';
                $r->status_class = 'bg-info';
            } elseif ($endDate < $today) {
                $r->status = 'Expired';
                $r->status_class = 'bg-secondary';
            } else {
                $r->status = 'Active';
                $r->status_class = 'bg-success';
            }
            
            $r->publish_status = $r->publish ? 'Published' : 'Draft';
            $r->publish_class = $r->publish ? 'bg-success' : 'bg-warning';
            
            // Format dates
            $r->formatted_start_date = $startDate->format('d/m/Y');
            $r->formatted_end_date = $endDate->format('d/m/Y');
            $r->formatted_created_at = Carbon::parse($r->created_at)->format('d/m/Y H:i');
            
            // Format shift times
            $r->formatted_start_time = $r->start_time ? Carbon::parse($r->start_time)->format('H:i') : '';
            $r->formatted_end_time = $r->end_time ? Carbon::parse($r->end_time)->format('H:i') : '';
            
            return $r;
        });

        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $rows
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in getScheduleReport: ' . $e->getMessage());
        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Schedule Report - CSV
 */
public function exportScheduleReport(Request $request)
{
    try {
        $branchFilter = $this->getBranchFilter();

        $query = DB::table('schedule')
            ->join('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
            ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
            ->select([
                'allemployees.employeeid',
                DB::raw('CONCAT(allemployees.firstname, " ", allemployees.lastname) as employee_name'),
                'department.department as department_name',
                'branches.name as branch_name',
                'shifts.shift_name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.break_time',
                'schedule.schedule_start_date',
                'schedule.schedule_end_date',
                'schedule.repeat_every_week',
                'schedule.publish',
                'schedule.is_current',
                'schedule.created_at'
            ])
            ->where('schedule.deleted_at', 0)
            ->where('allemployees.deleted_at', 0);

        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }

        // Apply filters (same as getScheduleReport)
        if ($request->filled('date_from')) {
            $query->whereDate('schedule.schedule_start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('schedule.schedule_end_date', '<=', $request->date_to);
        }
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
        }
        if ($request->filled('employee_name')) {
            $name = $request->employee_name;
            $query->where(function ($q) use ($name) {
                $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('department_id')) {
            $query->where('schedule.department_id', $request->department_id);
        }
        if (!$branchFilter && $request->filled('branch_id')) {
            $query->where('allemployees.branch_id', $request->branch_id);
        }
        if ($request->filled('shift_id')) {
            $query->where('schedule.shift_id', $request->shift_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('schedule.is_current', 1);
            } elseif ($request->status === 'upcoming') {
                $query->where('schedule.schedule_start_date', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('schedule.schedule_end_date', '<', now());
            }
        }
        if ($request->filled('publish_status') && $request->publish_status !== 'all') {
            $query->where('schedule.publish', $request->publish_status);
        }

        $rows = $query->orderBy('schedule.schedule_start_date', 'desc')->get();

        $filename = 'schedule_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM

            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Department',
                'Branch',
                'Shift Name',
                'Start Time',
                'End Time',
                'Break (min)',
                'Schedule Start Date',
                'Schedule End Date',
                'Repeat Every (weeks)',
                'Publish Status',
                'Current Status',
                'Created At'
            ]);

            foreach ($rows as $r) {
                // Determine status
                $today = now();
                $startDate = Carbon::parse($r->schedule_start_date);
                $endDate = Carbon::parse($r->schedule_end_date);
                
                if ($startDate > $today) {
                    $status = 'Upcoming';
                } elseif ($endDate < $today) {
                    $status = 'Expired';
                } else {
                    $status = 'Active';
                }

                fputcsv($file, [
                    $r->employeeid,
                    $r->employee_name,
                    $r->department_name,
                    $r->branch_name,
                    $r->shift_name,
                    $r->start_time ? Carbon::parse($r->start_time)->format('H:i') : '',
                    $r->end_time ? Carbon::parse($r->end_time)->format('H:i') : '',
                    $r->break_time,
                    $r->schedule_start_date ? Carbon::parse($r->schedule_start_date)->format('d/m/Y') : '',
                    $r->schedule_end_date ? Carbon::parse($r->schedule_end_date)->format('d/m/Y') : '',
                    $r->repeat_every_week,
                    $r->publish ? 'Published' : 'Draft',
                    $status,
                    $r->created_at ? Carbon::parse($r->created_at)->format('d/m/Y H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        \Log::error('Error in exportScheduleReport: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
    }
}

/**
 * Schedule Report - PDF
 */
public function exportScheduleReportPdf(Request $request)
{
    $branchFilter = $this->getBranchFilter();

    $query = DB::table('schedule')
        ->join('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
        ->leftJoin('department', 'schedule.department_id', '=', 'department.id')
        ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
        ->leftJoin('shifts', 'schedule.shift_id', '=', 'shifts.id')
        ->select([
            'allemployees.employeeid',
            DB::raw('CONCAT(allemployees.firstname, " ", allemployees.lastname) as employee_name'),
            'department.department as department_name',
            'branches.name as branch_name',
            'shifts.shift_name',
            'shifts.start_time',
            'shifts.end_time',
            'shifts.break_time',
            'schedule.schedule_start_date',
            'schedule.schedule_end_date',
            'schedule.repeat_every_week',
            'schedule.publish',
            'schedule.is_current',
            'schedule.created_at'
        ])
        ->where('schedule.deleted_at', 0)
        ->where('allemployees.deleted_at', 0);

    if ($branchFilter) {
        $query->where('allemployees.branch_id', $branchFilter);
    }

    // Apply filters (same as getScheduleReport)
    if ($request->filled('date_from')) {
        $query->whereDate('schedule.schedule_start_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('schedule.schedule_end_date', '<=', $request->date_to);
    }
    if ($request->filled('employee_id')) {
        $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
    }
    if ($request->filled('employee_name')) {
        $name = $request->employee_name;
        $query->where(function ($q) use ($name) {
            $q->where('allemployees.firstname', 'like', '%' . $name . '%')
              ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
        });
    }
    if ($request->filled('department_id')) {
        $query->where('schedule.department_id', $request->department_id);
    }
    if (!$branchFilter && $request->filled('branch_id')) {
        $query->where('allemployees.branch_id', $request->branch_id);
    }
    if ($request->filled('shift_id')) {
        $query->where('schedule.shift_id', $request->shift_id);
    }
    if ($request->filled('status')) {
        if ($request->status === 'active') {
            $query->where('schedule.is_current', 1);
        } elseif ($request->status === 'upcoming') {
            $query->where('schedule.schedule_start_date', '>', now());
        } elseif ($request->status === 'expired') {
            $query->where('schedule.schedule_end_date', '<', now());
        }
    }
    if ($request->filled('publish_status') && $request->publish_status !== 'all') {
        $query->where('schedule.publish', $request->publish_status);
    }

    $rows = $query->orderBy('schedule.schedule_start_date', 'desc')->get();

    // Format data for PDF
    $formattedRows = $rows->map(function($r) {
        $today = now();
        $startDate = Carbon::parse($r->schedule_start_date);
        $endDate = Carbon::parse($r->schedule_end_date);
        
        if ($startDate > $today) {
            $status = 'Upcoming';
        } elseif ($endDate < $today) {
            $status = 'Expired';
        } else {
            $status = 'Active';
        }

        return [
            'employee_id' => $r->employeeid,
            'employee_name' => $r->employee_name,
            'department' => $r->department_name,
            'branch' => $r->branch_name,
            'shift_name' => $r->shift_name,
            'start_time' => $r->start_time ? Carbon::parse($r->start_time)->format('H:i') : '',
            'end_time' => $r->end_time ? Carbon::parse($r->end_time)->format('H:i') : '',
            'break_time' => $r->break_time,
            'schedule_start' => $r->schedule_start_date ? Carbon::parse($r->schedule_start_date)->format('d/m/Y') : '',
            'schedule_end' => $r->schedule_end_date ? Carbon::parse($r->schedule_end_date)->format('d/m/Y') : '',
            'repeat_weeks' => $r->repeat_every_week,
            'publish_status' => $r->publish ? 'Published' : 'Draft',
            'current_status' => $status,
            'created_at' => $r->created_at ? Carbon::parse($r->created_at)->format('d/m/Y H:i') : ''
        ];
    });

    $pdf = Pdf::loadView('hrms.hr.Reports.Employee.pdf-schedule-report', [
        'rows' => $formattedRows,
        'generated_at' => now()->format('d/m/Y H:i'),
        'title' => 'Schedule Report',
        'filters' => [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'employee_id' => $request->employee_id,
            'employee_name' => $request->employee_name,
            'department_id' => $request->department_id,
            'branch_id' => $request->branch_id,
            'shift_id' => $request->shift_id,
            'status' => $request->status,
            'publish_status' => $request->publish_status
        ]
    ])->setPaper('a4', 'landscape');

    return $pdf->download('schedule_report_' . date('Y-m-d') . '.pdf');
}
public function getLeaveReport(Request $request)
{
    try {
        $branchFilter = $this->getBranchFilter();

        $query = DB::table('employee_leaves')
            ->join('allemployees', 'employee_leaves.employee_id', '=', 'allemployees.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->select([
                'employee_leaves.id',
                'employee_leaves.from_date',
                'employee_leaves.to_date',
                'employee_leaves.no_of_days',
                'employee_leaves.leave_type',
                'employee_leaves.leave_reason',
                'employee_leaves.status',
                'employee_leaves.created_at',
                'employee_leaves.updated_at',
                'employee_leaves.deleted_at',
                'allemployees.employeeid',
                'allemployees.firstname',
                'allemployees.lastname',
                'department.department as department_name',
                'branches.name as branch_name'
            ]);

        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('employee_leaves.from_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('employee_leaves.to_date', '<=', $request->date_to);
        }
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
        }
        if ($request->filled('employee_name')) {
            $name = $request->employee_name;
            $query->where(function ($q) use ($name) {
                $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('department_id')) {
            $query->where('allemployees.department', $request->department_id);
        }
        if (!$branchFilter && $request->filled('branch_id')) {
            $query->where('allemployees.branch_id', $request->branch_id);
        }
        if ($request->filled('leave_type') && $request->leave_type !== 'all') {
            $query->where('employee_leaves.leave_type', $request->leave_type);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('employee_leaves.status', $request->status);
        }

        $totalRecords = (clone $query)->count();

        $orderColumn = null;
        $orderDir = 'desc';
        if ($request->has('order')) {
            $dir = $request->order[0]['dir'] ?? 'desc';
            $colKey = $request->columns[$request->order[0]['column']]['data'] ?? null;
            $map = [
                'from_date' => 'employee_leaves.from_date',
                'to_date' => 'employee_leaves.to_date',
                'employeeid' => 'allemployees.employeeid',
                'full_name' => null,
                'department_name' => 'department.department',
                'branch_name' => 'branches.name',
                'leave_type' => 'employee_leaves.leave_type',
                'no_of_days' => 'employee_leaves.no_of_days',
                'status' => 'employee_leaves.status',
                'created_at' => 'employee_leaves.created_at',
            ];
            if (array_key_exists($colKey, $map)) {
                $orderColumn = $map[$colKey];
                $orderDir = in_array(strtolower($dir), ['asc', 'desc']) ? $dir : 'desc';
            }
        }

        if ($orderColumn === null && isset($colKey) && $colKey === 'full_name') {
            $query->orderBy('allemployees.firstname', $orderDir)
                  ->orderBy('allemployees.lastname', $orderDir);
        } elseif ($orderColumn) {
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->orderBy('employee_leaves.from_date', 'desc');
        }

        if ($request->has('start') && $request->has('length')) {
            $query->offset((int) $request->start)->limit((int) $request->length);
        }

        $rows = $query->get()->map(function ($r) {
            $r->full_name = trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? ''));
            return $r;
        });

        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $rows
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in getLeaveReport: ' . $e->getMessage());
        return response()->json([
            'draw' => (int) ($request->draw ?? 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}
public function exportLeaveReport(Request $request)
{
    try {
        $branchFilter = $this->getBranchFilter();

        $query = DB::table('employee_leaves')
            ->join('allemployees', 'employee_leaves.employee_id', '=', 'allemployees.id')
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->select([
                'allemployees.employeeid',
                DB::raw('CONCAT(allemployees.firstname, " ", allemployees.lastname) as employee_name'),
                'department.department as department_name',
                'branches.name as branch_name',
                'employee_leaves.leave_type',
                'employee_leaves.from_date',
                'employee_leaves.to_date',
                'employee_leaves.no_of_days',
                'employee_leaves.leave_reason',
                'employee_leaves.status',
                'employee_leaves.created_at'
            ]);

        if ($branchFilter) {
            $query->where('allemployees.branch_id', $branchFilter);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('employee_leaves.from_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('employee_leaves.to_date', '<=', $request->date_to);
        }
        if ($request->filled('employee_id')) {
            $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
        }
        if ($request->filled('employee_name')) {
            $name = $request->employee_name;
            $query->where(function ($q) use ($name) {
                $q->where('allemployees.firstname', 'like', '%' . $name . '%')
                  ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
            });
        }
        if ($request->filled('department_id')) {
            $query->where('allemployees.department', $request->department_id);
        }
        if (!$branchFilter && $request->filled('branch_id')) {
            $query->where('allemployees.branch_id', $request->branch_id);
        }
        if ($request->filled('leave_type') && $request->leave_type !== 'all') {
            $query->where('employee_leaves.leave_type', $request->leave_type);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('employee_leaves.status', $request->status);
        }

        $rows = $query->orderBy('employee_leaves.from_date', 'desc')->get();

        $filename = 'leave_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM

            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Department',
                'Branch',
                'Leave Type',
                'From Date',
                'To Date',
                'Days',
                'Reason',
                'Status',
                'Created At'
            ]);

            foreach ($rows as $r) {
                fputcsv($file, [
                    $r->employeeid,
                    $r->employee_name,
                    $r->department_name,
                    $r->branch_name,
                    $r->leave_type,
                    $r->from_date ? date('d/m/Y', strtotime($r->from_date)) : '',
                    $r->to_date ? date('d/m/Y', strtotime($r->to_date)) : '',
                    $r->no_of_days,
                    $r->leave_reason,
                    ucfirst($r->status),
                    $r->created_at ? date('d/m/Y H:i', strtotime($r->created_at)) : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        \Log::error('Error in exportLeaveReport: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
    }
}
public function exportLeaveReportPdf(Request $request)
{
    $branchFilter = $this->getBranchFilter();

    $query = DB::table('employee_leaves')
        ->join('allemployees', 'employee_leaves.employee_id', '=', 'allemployees.id')
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
        ->select([
            'allemployees.employeeid',
            DB::raw('CONCAT(allemployees.firstname, " ", allemployees.lastname) as employee_name'),
            'department.department as department_name',
            'branches.name as branch_name',
            'employee_leaves.leave_type',
            'employee_leaves.from_date',
            'employee_leaves.to_date',
            'employee_leaves.no_of_days',
            'employee_leaves.leave_reason',
            'employee_leaves.status',
            'employee_leaves.created_at'
        ]);

    if ($branchFilter) {
        $query->where('allemployees.branch_id', $branchFilter);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('employee_leaves.from_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('employee_leaves.to_date', '<=', $request->date_to);
    }
    if ($request->filled('employee_id')) {
        $query->where('allemployees.employeeid', 'like', '%' . $request->employee_id . '%');
    }
    if ($request->filled('employee_name')) {
        $name = $request->employee_name;
        $query->where(function ($q) use ($name) {
            $q->where('allemployees.firstname', 'like', '%' . $name . '%')
              ->orWhere('allemployees.lastname', 'like', '%' . $name . '%');
        });
    }
    if ($request->filled('department_id')) {
        $query->where('allemployees.department', $request->department_id);
    }
    if (!$branchFilter && $request->filled('branch_id')) {
        $query->where('allemployees.branch_id', $request->branch_id);
    }
    if ($request->filled('leave_type') && $request->leave_type !== 'all') {
        $query->where('employee_leaves.leave_type', $request->leave_type);
    }
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('employee_leaves.status', $request->status);
    }

    $rows = $query->orderBy('employee_leaves.from_date', 'desc')->get();

    $formattedRows = $rows->map(function($r) {
        return [
            'employee_id' => $r->employeeid,
            'employee_name' => $r->employee_name,
            'department' => $r->department_name,
            'branch' => $r->branch_name,
            'leave_type' => $r->leave_type,
            'from_date' => $r->from_date ? date('d/m/Y', strtotime($r->from_date)) : '',
            'to_date' => $r->to_date ? date('d/m/Y', strtotime($r->to_date)) : '',
            'days' => $r->no_of_days,
            'reason' => $r->leave_reason,
            'status' => ucfirst($r->status),
            'created_at' => $r->created_at ? date('d/m/Y H:i', strtotime($r->created_at)) : ''
        ];
    });

    $pdf = Pdf::loadView('hrms.hr.Reports.Employee.pdf-leave-report', [
        'rows' => $formattedRows,
        'generated_at' => now()->format('d/m/Y H:i'),
        'title' => 'Leave Report',
        'filters' => [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'employee_id' => $request->employee_id,
            'employee_name' => $request->employee_name,
            'department_id' => $request->department_id,
            'branch_id' => $request->branch_id,
            'leave_type' => $request->leave_type,
            'status' => $request->status
        ]
    ])->setPaper('a4', 'landscape');

    return $pdf->download('leave_report_' . date('Y-m-d') . '.pdf');
}
}