<?php

namespace App\Http\Controllers\Backend\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CompOffController extends Controller
{
    public function index()
    {
        $employeeId = Session::get('user_id');
        
        $compOffs = DB::table('comp_offs')
            ->join('overtime_records', 'comp_offs.overtime_record_id', '=', 'overtime_records.id')
            ->where('comp_offs.employee_id', $employeeId)
            ->select('comp_offs.*', 'overtime_records.overtime_date', 'overtime_records.overtime_hours')
            ->orderBy('comp_offs.created_at', 'desc')
            ->paginate(15);

        // Get available overtime records for comp-off
        $availableOvertimes = DB::table('overtime_records')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('comp_offs')
                    ->whereRaw('comp_offs.overtime_record_id = overtime_records.id');
            })
            ->orderBy('overtime_date', 'desc')
            ->get();

        return view('hrms.comp-off.index', compact('compOffs', 'availableOvertimes'));
    }

    public function store(Request $request)
    {
        $employeeId = Session::get('user_id');
        
        $request->validate([
            'overtime_record_id' => 'required|exists:overtime_records,id',
            'comp_off_date' => 'required|date|after:today',
            'reason' => 'required|string|max:500'
        ]);

        // Verify overtime record belongs to employee and is approved
        $overtimeRecord = DB::table('overtime_records')
            ->where('id', $request->overtime_record_id)
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->first();

        if (!$overtimeRecord) {
            return redirect()->back()->with('error', 'Invalid overtime record selected!');
        }

        // Check if comp-off date is a working day
        $compOffDate = Carbon::parse($request->comp_off_date);
        if ($compOffDate->dayOfWeek === Carbon::SUNDAY || $this->isHoliday($request->comp_off_date)) {
            return redirect()->back()->with('error', 'Comp-off cannot be taken on holidays or Sundays!');
        }

        // Check if comp-off already exists for this overtime
        $existingCompOff = DB::table('comp_offs')
            ->where('overtime_record_id', $request->overtime_record_id)
            ->first();

        if ($existingCompOff) {
            return redirect()->back()->with('error', 'Comp-off already requested for this overtime!');
        }

        DB::table('comp_offs')->insert([
            'employee_id' => $employeeId,
            'overtime_record_id' => $request->overtime_record_id,
            'comp_off_date' => $request->comp_off_date,
            'reason' => $request->reason,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Comp-off request submitted successfully!');
    }

    public function adminIndex()
    {
        $compOffs = DB::table('comp_offs')
            ->join('allemployees', 'comp_offs.employee_id', '=', 'allemployees.id')
            ->join('overtime_records', 'comp_offs.overtime_record_id', '=', 'overtime_records.id')
            ->select(
                'comp_offs.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'overtime_records.overtime_date',
                'overtime_records.overtime_hours'
            )
            ->orderBy('comp_offs.created_at', 'desc')
            ->paginate(20);

        return view('hrms.comp-off.admin.index', compact('compOffs'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        DB::table('comp_offs')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'approved_by' => Session::get('user_id'),
                'approved_at' => now(),
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    private function isHoliday($date)
    {
        return DB::table('holidays')
            ->whereDate('holidaydate', $date)
            ->exists();
    }
}
