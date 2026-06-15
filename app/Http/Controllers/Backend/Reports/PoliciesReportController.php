<?php
namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PoliciesReportController extends Controller
{
    // Display all policies with reporting features
    public function index(Request $request)
    {
        // Get filter parameters
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $departmentId = $request->input('department_id');

        // Base query with department join
        $query = DB::table('policies')
            ->leftJoin('department', 'policies.department', '=', 'department.id')
            ->select(
                'policies.id',
                'policies.policy_name',
                'policies.description',
                'policies.file_path',
                'policies.created_at',
                'policies.department as department_id',
                'department.department as department_name'
            );

        // Apply filters
        if ($fromDate) {
            $query->whereDate('policies.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('policies.created_at', '<=', $toDate);
        }
        if ($departmentId) {
            $query->where('policies.department', $departmentId);
        }

        $policies = $query->get();
        $departments = DB::table('department')->select('id', 'department')->get();

        return view('hrms.hr.Reports.Policy.index', compact('policies', 'departments'));
    }

    // Export to CSV
    public function exportCsv(Request $request)
    {
        $fileName = 'policies_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        // Get filtered policies with department info
        $policies = DB::table('policies')
            ->leftJoin('department', 'policies.department', '=', 'department.id')
            ->select(
                'policies.id',
                'policies.policy_name',
                'policies.description',
                'policies.department as department_id',
                'department.department as department_name',
                'policies.created_at'
            );

        // Apply filters
        if ($fromDate = $request->input('from')) {
            $policies->whereDate('policies.created_at', '>=', $fromDate);
        }
        if ($toDate = $request->input('to')) {
            $policies->whereDate('policies.created_at', '<=', $toDate);
        }
        if ($departmentId = $request->input('department_id')) {
            $policies->where('policies.department', $departmentId);
        }

        $policies = $policies->get();

        $callback = function() use ($policies) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 
                'Policy Name', 
                'Description',
                'Department ID',
                'Department Name', 
                'Created Date'
            ]);

            // Add data rows
            foreach ($policies as $policy) {
                fputcsv($file, [
                    $policy->id,
                    $policy->policy_name,
                    $policy->description,
                    $policy->department_id ?? 'N/A',
                    $policy->department_name ?? 'N/A',
                    \Carbon\Carbon::parse($policy->created_at)->format('Y-m-d')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export to PDF
    public function exportPdf(Request $request)
    {
        $policies = $this->getFilteredPolicies($request);
        $pdf = \PDF::loadView('hrms.Hr.Reports.Policy.pdf', compact('policies'));
        
        return $pdf->download('policies_report_' . date('Y-m-d') . '.pdf');
    }

    // Helper method to get filtered policies
    private function getFilteredPolicies(Request $request)
    {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $departmentId = $request->input('department_id');

        $query = DB::table('policies')
            ->leftJoin('department', 'policies.department', '=', 'department.id')
            ->select(
                'policies.id',
                'policies.policy_name',
                'policies.description',
                'policies.department as department_id',
                'department.department as department_name',
                'policies.created_at'
            );

        if ($fromDate) {
            $query->whereDate('policies.created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('policies.created_at', '<=', $toDate);
        }
        if ($departmentId) {
            $query->where('policies.department', $departmentId);
        }

        return $query->get();
    }
}