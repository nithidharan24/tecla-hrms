<?php

namespace App\Http\Controllers\Backend\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use PDF;

class UserReportController extends Controller
{
    public function index(Request $request)
    {
        $users = $this->getFilteredUsers($request);
        $allUsers = DB::table('users')->select('id', 'first_name', 'last_name')->get();
        
        return view('hrms.hr.Reports.users-reports.index', compact('users', 'allUsers'));
    }

    public function exportCSV(Request $request)
    {
        $users = $this->getFilteredUsers($request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'S.No',
                'Name',
                'Email',
                'Role',
                'Status',
                'Designation'
            ]);

            // Add data rows
            foreach ($users as $index => $user) {
                fputcsv($file, [
                    $index + 1,
                    $user->first_name . ' ' . $user->last_name,
                    $user->email,
                    $user->role,
                    ucfirst($user->status),
                    $user->designation ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $users = $this->getFilteredUsers($request);

        $data = [
            'title' => 'User Report - ' . date('Y-m-d'),
            'users' => $users,
            'filter' => $request->user_name ? DB::table('users')
                ->where('id', $request->user_name)
                ->select(DB::raw("CONCAT(first_name, ' ', last_name) as name"))
                ->first()->name : 'All Users'
        ];

        $pdf = PDF::loadView('hrms.hr.Reports.users-reports.pdf', $data);
        
        return $pdf->download('user_report_' . date('Y-m-d') . '.pdf');
    }

    private function getFilteredUsers(Request $request)
    {
        $query = DB::table('users')
            ->leftJoin('designation', 'users.id', '=', 'designation.id')
            ->leftJoin('allemployees', 'users.id', '=', 'allemployees.employeeid')
            ->select(
                'users.id', 
                'users.first_name', 
                'users.last_name', 
                'users.email',  
                'users.role', 
                'users.status', 
                'designation.designation', 
                'allemployees.profile_image'
            );

        if ($request->filled('user_name')) {
            $query->where('users.id', $request->user_name);
        }

        return $query->get();
    }

    public function changeStatus(Request $request, $id)
    {
        $status = $request->input('status');
        DB::table('users')->where('id', $id)->update(['status' => $status]);
        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}