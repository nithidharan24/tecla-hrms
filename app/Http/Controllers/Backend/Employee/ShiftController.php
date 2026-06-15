<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftController extends Controller
{
    // Display all shifts with enhanced details
    public function index()
    {
        $shifts = DB::table('shifts')
            ->where('deleted_at', 0)
            ->get()
            ->map(function ($shift) {
                $shift->start_time = date('h:i A', strtotime($shift->start_time));
                $shift->end_time = date('h:i A', strtotime($shift->end_time));
                $shift->break_time = $shift->break_time ? $shift->break_time . ' mins' : 'None';
                $shift->days = $shift->days_of_week ? explode(',', $shift->days_of_week) : [];
                
                // Check if Sunday is included
                $shift->includes_sunday = $this->checkSundayInShift($shift->days_of_week);
                
                // Get employee count for this shift
                $shift->employee_count = DB::table('schedule')
                    ->where('shift_id', $shift->id)
                    ->where('deleted_at', 0)
                    ->where('is_current', 1)
                    ->count();
                
                // Calculate total working hours per week
                $shift->weekly_hours = count($shift->days) * $this->calculateDailyHours($shift->id);
                
                return $shift;
            });

        // Get shifts that include Sunday
        $sundayShifts = $shifts->where('includes_sunday', true);
        
        // Get statistics
        $stats = [
            'total_shifts' => $shifts->count(),
            'sunday_shifts' => $sundayShifts->count(),
            'active_shifts' => $shifts->where('status', 'active')->count(),
            'total_employees_on_shifts' => $shifts->sum('employee_count')
        ];

        return view('hrms.Employee.Shift.index', compact('shifts', 'sundayShifts', 'stats'));
    }

    // Show create form
    public function create()
    {
        return view('hrms.Employee.Shift.create');
    }

    // Store new shift
    public function store(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|max:25',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array|min:1',
            'break_time' => 'nullable|integer|min:0',
        ]);

        DB::table('shifts')->insert([
            'shift_name' => $request->shift_name,
            'start_time' => Carbon::createFromFormat('H:i', $request->start_time)->format('H:i:s'),
            'end_time' => Carbon::createFromFormat('H:i', $request->end_time)->format('H:i:s'),
            'break_time' => $request->break_time,
            'days_of_week' => implode(',', $request->days_of_week),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('shift.index')->with('success', 'Shift added successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $shift = DB::table('shifts')->where('id', $id)->first();

        if (!$shift) {
            return redirect()->route('shift.index')->with('error', 'Shift not found');
        }

        // Convert days string to array for the form
        $shift->days_of_week = $shift->days_of_week ? explode(',', $shift->days_of_week) : [];

        return view('hrms.Employee.Shift.edit', compact('shift'));
    }

    // Update shift
    public function update(Request $request, $id)
    {
        $request->validate([
            'shift_name' => 'required|max:25',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array|min:1',
            'break_time' => 'nullable|integer|min:0',
        ]);

        DB::table('shifts')->where('id', $id)->update([
            'shift_name' => $request->shift_name,
            'start_time' => Carbon::createFromFormat('H:i', $request->start_time)->format('H:i:s'),
            'end_time' => Carbon::createFromFormat('H:i', $request->end_time)->format('H:i:s'),
            'break_time' => $request->break_time,
            'days_of_week' => implode(',', $request->days_of_week),
            'updated_at' => now(),
        ]);

        return redirect()->route('shift.index')->with('success', 'Shift updated successfully');
    }

    // Delete shift (soft delete)
    public function destroy($id)
    {
        // Check if shift is being used by any active schedules
        $activeSchedules = DB::table('schedule')
            ->where('shift_id', $id)
            ->where('deleted_at', 0)
            ->where('is_current', 1)
            ->count();

        if ($activeSchedules > 0) {
            return redirect()->route('shift.index')
                ->with('error', "Cannot delete shift. It is currently assigned to {$activeSchedules} active schedule(s).");
        }

        DB::table('shifts')
            ->where('id', $id)
            ->update(['deleted_at' => 1]);

        return redirect()->route('shift.index')->with('success', 'Shift deleted successfully');
    }

    // Update shift status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        DB::table('shifts')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    // Get shift details with employees
    public function show($id)
    {
        $shift = DB::table('shifts')->where('id', $id)->first();
        
        if (!$shift) {
            return redirect()->route('shift.index')->with('error', 'Shift not found');
        }

        // Get employees assigned to this shift
        $employees = DB::table('schedule')
            ->join('allemployees', 'schedule.employee_id', '=', 'allemployees.id')
            ->join('department', 'schedule.department_id', '=', 'department.id')
            ->where('schedule.shift_id', $id)
            ->where('schedule.deleted_at', 0)
            ->where('schedule.is_current', 1)
            ->select(
                'allemployees.*',
                'department.department',
                'schedule.schedule_start_date',
                'schedule.schedule_end_date'
            )
            ->get();

        // Format shift data
        $shift->days = $shift->days_of_week ? explode(',', $shift->days_of_week) : [];
        $shift->includes_sunday = $this->checkSundayInShift($shift->days_of_week);
        $shift->formatted_start_time = date('h:i A', strtotime($shift->start_time));
        $shift->formatted_end_time = date('h:i A', strtotime($shift->end_time));

        return view('hrms.Employee.Shift.show', compact('shift', 'employees'));
    }

    // Check if Sunday is included in shift
    private function checkSundayInShift($daysOfWeek)
    {
        if (empty($daysOfWeek)) {
            return false;
        }

        $days = array_map('trim', explode(',', $daysOfWeek));
        
        foreach ($days as $day) {
            $day = strtolower(trim($day));
            
            // Check various Sunday formats
            if (in_array($day, ['sunday', 'sun', '0', '7'])) {
                return true;
            }
        }
        
        return false;
    }

    // Calculate daily working hours for a shift
    private function calculateDailyHours($shiftId)
    {
        $shift = DB::table('shifts')->find($shiftId);
        
        if (!$shift) {
            return 0;
        }

        $start = Carbon::parse($shift->start_time);
        $end = Carbon::parse($shift->end_time);
        $breakTime = $shift->break_time ?? 0;
        
        $totalMinutes = $start->diffInMinutes($end) - $breakTime;
        
        return $totalMinutes / 60; // Convert to hours
    }

    // Get shifts that work on specific day
    public function getShiftsByDay(Request $request)
    {
        $day = $request->input('day'); // monday, tuesday, etc.
        
        $shifts = DB::table('shifts')
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->get()
            ->filter(function ($shift) use ($day) {
                $days = array_map('strtolower', array_map('trim', explode(',', $shift->days_of_week)));
                return in_array(strtolower($day), $days);
            });

        return response()->json($shifts);
    }

    // Bulk update working days
    public function bulkUpdateWorkingDays(Request $request)
    {
        $request->validate([
            'shift_ids' => 'required|array',
            'action' => 'required|in:add_sunday,remove_sunday,add_saturday,remove_saturday',
        ]);

        $shiftIds = $request->shift_ids;
        $action = $request->action;

        foreach ($shiftIds as $shiftId) {
            $shift = DB::table('shifts')->find($shiftId);
            
            if (!$shift) {
                continue;
            }

            $days = $shift->days_of_week ? array_map('trim', explode(',', $shift->days_of_week)) : [];
            
            switch ($action) {
                case 'add_sunday':
                    if (!$this->checkSundayInShift($shift->days_of_week)) {
                        $days[] = 'Sunday';
                    }
                    break;
                    
                case 'remove_sunday':
                    $days = array_filter($days, function($day) {
                        return !in_array(strtolower(trim($day)), ['sunday', 'sun', '0', '7']);
                    });
                    break;
                    
                case 'add_saturday':
                    if (!in_array('Saturday', $days) && !in_array('Sat', $days) && !in_array('6', $days)) {
                        $days[] = 'Saturday';
                    }
                    break;
                    
                case 'remove_saturday':
                    $days = array_filter($days, function($day) {
                        return !in_array(strtolower(trim($day)), ['saturday', 'sat', '6']);
                    });
                    break;
            }

            DB::table('shifts')->where('id', $shiftId)->update([
                'days_of_week' => implode(',', array_unique($days)),
                'updated_at' => now()
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Working days updated successfully']);
    }
}
