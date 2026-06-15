<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = DB::table('leave_types')->get();
        return view('hrms.Settings.LeaveType.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        DB::table('leave_types')->insert([
            'leave_type' => $request->leave_type,
            'leave_days' => $request->leave_days,
            'status' => 'Active'
        ]);
        return redirect()->route('leave-types.index')->with('success', 'Leave Type added successfully.');
    }

    public function update(Request $request, $id)
    {
        DB::table('leave_types')
            ->where('id', $id)
            ->update([
                'leave_type' => $request->leave_type,
                'leave_days' => $request->leave_days,
                'status' => $request->status
            ]);
        return redirect()->route('leave-types.index')->with('success', 'Leave Type updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('leave_types')->where('id', $id)->delete();
        return redirect()->route('leave-types.index')->with('success', 'Leave Type deleted successfully.');
    }
    public function updateStatus($id, $status)
{
    \Log::info('Update Status Called', ['id' => $id, 'status' => $status]); // Debugging line

    // Check if the status is valid
    if (!in_array($status, ['active', 'inactive'])) {
        return response()->json(['success' => false, 'message' => 'Invalid status.'], 400);
    }

    // Use DB methods to update the status
    $updated = DB::table('leave_types') // Replace 'leave_types' with your actual table name
        ->where('id', $id)
        ->update(['status' => $status]);

    // Check if any row was updated
    if ($updated) {
        // Return a success response
        return response()->json(['success' => true]);
    }

    // Return an error response if leave type not found or update failed
    return response()->json(['success' => false, 'message' => 'Leave type not found or status not updated.'], 404);
}
    

}
