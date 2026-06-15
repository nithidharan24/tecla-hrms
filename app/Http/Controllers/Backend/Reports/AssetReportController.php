<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use App\Exports\AssetsExport;
use Maatwebsite\Excel\Facades\Excel;

class AssetReportController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildReportQuery($request);
        $branches = $this->getBranchesForFilter();

        if ($request->has('export')) {
            return $this->handleExport($query->get(), $request);
        }

        $assets = $query->paginate(25); // Added pagination
        
        // Calculate summary statistics
        $summary = $this->getAssetSummaryStatistics($request);

        return view('hrms.hr.Reports.AssetsExport.index', [
            'assets' => $assets,
            'branches' => $branches,
            'isAdmin' => session('role') === 'admin',
            'summary' => $summary // Added summary data
        ]);
    }

    protected function getAssetSummaryStatistics(Request $request)
    {
        $baseQuery = $this->buildBaseQuery($request);
        
        return [
            'total_assets' => $baseQuery->count(),
            'assigned_assets' => $baseQuery->whereNotNull('asset_user')->count(),
            'unassigned_assets' => $baseQuery->whereNull('asset_user')->count(),
            'pending_assets' => $baseQuery->where('status', 'pending')->count(),
            'approved_assets' => $baseQuery->where('status', 'approved')->count(),
            'returned_assets' => $baseQuery->where('status', 'returned')->count(),
            'total_value' => $baseQuery->sum('value')
        ];
    }

    protected function buildBaseQuery(Request $request)
    {
        $query = DB::table('assets_company')
            ->where('deleted_at', 0);

        // Apply the same filters as the main query
        $this->applyCommonFilters($query, $request);
        
        return $query;
    }

    protected function buildReportQuery(Request $request)
    {
        $query = DB::table('assets_company')
            ->leftJoin('allemployees', 'assets_company.asset_user', '=', 'allemployees.id')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->leftJoin('branches', 'allemployees.branch_id', '=', 'branches.id')
            ->select(
                'assets_company.*',
                'allemployees.firstname',
                'allemployees.lastname',
                'designation.designation as designation_name',
                'branches.name as name'
            )
            ->where('assets_company.deleted_at', 0);

        $this->applyCommonFilters($query, $request);
        return $query;
    }

    protected function applyCommonFilters($query, Request $request)
    {
        // Apply branch restriction for employees
        if (session('role') === 'employee') {
            $query->where('allemployees.branch_id', session('branch_id'));
        }

        if ($request->filled('employee_name')) {
            $query->where(function($q) use ($request) {
                $q->where('allemployees.firstname', 'like', '%'.$request->employee_name.'%')
                  ->orWhere('allemployees.lastname', 'like', '%'.$request->employee_name.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('assets_company.status', $request->status);
        }

        if (session('role') === 'admin' && $request->filled('branch')) {
            $query->where('allemployees.branch_id', $request->branch);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            try {
                $fromDate = Carbon::createFromFormat('d/m/Y', $request->from_date)->startOfDay();
                $toDate = Carbon::createFromFormat('d/m/Y', $request->to_date)->endOfDay();
                $query->whereBetween('assets_company.purchase_date', [$fromDate, $toDate]);
            } catch (\Exception $e) {
                // Invalid date format
            }
        }
    }

    protected function getBranchesForFilter()
    {
        if (session('role') === 'admin') {
            return DB::table('branches')->get();
        }
        return DB::table('branches')->where('id', session('branch_id'))->get();
    }

    protected function handleExport($assets, Request $request)
    {
        $filters = $this->getAppliedFilters($request);
        $summary = $this->getAssetSummaryStatistics($request); // Add summary to export
        
        if ($request->export == 'pdf') {
            $pdf = PDF::loadView('hrms.hr.Reports.AssetsExport.pdf', [
                'assets' => $assets,
                'filters' => $filters,
                'summary' => $summary
            ]);
            return $pdf->download('assets-report-'.date('Y-m-d').'.pdf');
        }
        
        return Excel::download(
            new AssetsExport($assets, $filters, $summary), // Pass summary to export
            'assets-report-'.date('Y-m-d').'.csv'
        );
    }

    protected function getAppliedFilters(Request $request)
    {
        return [
            'employee_name' => $request->employee_name,
            'status' => $request->status,
            'branch' => $request->branch,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'generated_by' => session('admin_name') ?? session('first_name').' '.session('last_name'),
            'generated_at' => now()->format('d/m/Y H:i:s')
        ];
    }
}