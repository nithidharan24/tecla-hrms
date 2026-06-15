<?php

namespace App\Http\Controllers\Backend\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PDF;
use Carbon\Carbon;

class ExpenseReportController extends Controller
{
    public function index(Request $request) 
    {
        // Start the query to fetch expenses
        $query = DB::table('expenses')
            ->select(
                'expenses.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) as employee_full_name"),
                'allemployees.employeeid as employee_code',
                'allemployees.email as employee_email',
                'allemployees.phone as employee_phone'
            )
            ->leftJoin('allemployees', function($join) {
                $join->on('expenses.employee_id', '=', 'allemployees.employeeid');
            })
            ->where('expenses.deleted_at', 0);

        // Handle both expense types by mapping fields
        $query->selectRaw("
            CASE 
                WHEN expenses.item_name IS NOT NULL THEN expenses.item_name 
                ELSE 'Travel Expense' 
            END as display_item_name,
            CASE 
                WHEN expenses.purchase_from IS NOT NULL THEN expenses.purchase_from 
                ELSE expenses.place_of_visit 
            END as display_purchase_from,
            CASE 
                WHEN expenses.purchase_date IS NOT NULL THEN expenses.purchase_date 
                ELSE expenses.departure_date 
            END as display_purchase_date,
            CASE 
                WHEN expenses.amount IS NOT NULL THEN expenses.amount 
                ELSE expenses.expense_amount 
            END as display_amount,
            CASE 
                WHEN expenses.currency IS NOT NULL THEN expenses.currency 
                ELSE 'INR' 
            END as display_currency,
            CASE 
                WHEN expenses.purchased_by IS NOT NULL THEN expenses.purchased_by 
                ELSE (
                    SELECT CONCAT(firstname, ' ', lastname) 
                    FROM allemployees 
                    WHERE employeeid = expenses.employee_id
                ) 
            END as display_purchased_by
        ");

        // Check if the purchased_by filter is provided
        if ($request->has('purchased_by') && !empty($request->purchased_by)) {
            $query->where(function($q) use ($request) {
                $q->where('expenses.purchased_by', $request->purchased_by)
                  ->orWhere('expenses.employee_id', $request->purchased_by);
            });
        }

        // Check if employee filter is provided
        if ($request->has('employee_id') && !empty($request->employee_id)) {
            $query->where(function($q) use ($request) {
                $q->where('expenses.employee_id', $request->employee_id)
                  ->orWhere('allemployees.employeeid', $request->employee_id);
            });
        }

        // Check if date range filters are provided
        if ($request->has('from') && $request->has('to') && !empty($request->from) && !empty($request->to)) {
            $fromDate = date('Y-m-d', strtotime($request->from));
            $toDate = date('Y-m-d', strtotime($request->to));

            $query->where(function($q) use ($fromDate, $toDate) {
                $q->whereDate('expenses.purchase_date', '>=', $fromDate)
                  ->whereDate('expenses.purchase_date', '<=', $toDate)
                  ->orWhere(function($subq) use ($fromDate, $toDate) {
                      $subq->whereDate('expenses.departure_date', '>=', $fromDate)
                           ->whereDate('expenses.departure_date', '<=', $toDate);
                  });
            });
        }

        // Execute the query and get the results
        $expenses = $query->get();

        // Process expenses to ensure consistent data structure
        foreach ($expenses as $expense) {
            // Use display fields for consistent data
            $expense->item_name = $expense->display_item_name;
            $expense->purchase_from = $expense->display_purchase_from;
            $expense->purchase_date = $expense->display_purchase_date;
            $expense->amount = $expense->display_amount;
            $expense->currency = $expense->display_currency;
            
            // Set purchased_by from either direct field or employee name
            if (empty($expense->purchased_by)) {
                $expense->purchased_by = $expense->display_purchased_by;
            }
            
            // For travel expenses, use departure/arrival dates as context
            if (empty($expense->item_name) || $expense->item_name == 'Travel Expense') {
                $expense->is_travel_expense = true;
            }
        }

        // Fetch distinct buyers for the dropdown filter
        $buyers = DB::table('expenses')
            ->where(function($q) {
                $q->whereNotNull('purchased_by')
                  ->where('purchased_by', '!=', '')
                  ->orWhereNotNull('employee_id');
            })
            ->where('deleted_at', 0)
            ->distinct()
            ->get()
            ->map(function($item) {
                return $item->purchased_by ?? $item->employee_id;
            })
            ->filter()
            ->unique()
            ->values();

        // Fetch employees for better filtering
        $employees = DB::table('allemployees')
            ->select(
                'employeeid as id', 
                DB::raw("CONCAT(firstname, ' ', lastname) as name"),
                'employeeid as code'
            )
            ->where('deleted_at', 0)
            ->where('status', 'active')
            ->orderBy('firstname')
            ->get();

        return view('hrms.hr.Reports.expense-reports.index', compact('expenses', 'buyers', 'employees'));
    }

    private function getFilteredExpenses(Request $request)
    {
        $query = DB::table('expenses')
            ->select(
                'id', 
                'expense_id',
                'item_name', 
                'purchase_from', 
                'place_of_visit',
                'purchase_date', 
                'departure_date',
                'purchased_by', 
                'employee_id',
                'amount', 
                'expense_amount',
                'currency',
                'status', 
                'paid_by', 
                'attachments',
                'upload_receipt',
                'purpose_of_visit',
                'customer_name',
                'duration_days',
                'arrival_date',
                'is_billable'
            )
            ->where('deleted_at', 0);

        // Handle filtering across both expense types
        if ($request->has('purchased_by') && !empty($request->purchased_by)) {
            $query->where(function($q) use ($request) {
                $q->where('purchased_by', $request->purchased_by)
                  ->orWhere('employee_id', $request->purchased_by);
            });
        }

        if ($request->has('from') && $request->has('to') && !empty($request->from) && !empty($request->to)) {
            $fromDate = date('Y-m-d', strtotime($request->from));
            $toDate = date('Y-m-d', strtotime($request->to));

            $query->where(function($q) use ($fromDate, $toDate) {
                $q->whereDate('purchase_date', '>=', $fromDate)
                  ->whereDate('purchase_date', '<=', $toDate)
                  ->orWhere(function($subq) use ($fromDate, $toDate) {
                      $subq->whereDate('departure_date', '>=', $fromDate)
                           ->whereDate('departure_date', '<=', $toDate);
                  });
            });
        }

        return $query->get();
    }

    public function exportCSV(Request $request)
    {
        $expenses = $this->getFilteredExpenses($request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="expenses_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($expenses) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Expense ID',
                'Item Name / Purpose',
                'Purchase From / Place',
                'Date',
                'Purchased By / Employee',
                'Amount',
                'Currency',
                'Paid By',
                'Status',
                'Type'
            ]);

            // Add data rows
            foreach ($expenses as $expense) {
                $itemName = $expense->item_name ?? ($expense->purpose_of_visit ?? 'N/A');
                $purchaseFrom = $expense->purchase_from ?? $expense->place_of_visit ?? 'N/A';
                $date = $expense->purchase_date ?? $expense->departure_date ?? 'N/A';
                $purchasedBy = $expense->purchased_by ?? $expense->employee_id ?? 'N/A';
                $amount = $expense->amount ?? $expense->expense_amount ?? 0;
                $currency = $expense->currency ?? 'INR';
                $type = $expense->item_name ? 'Regular' : 'Travel';

                fputcsv($file, [
                    $expense->expense_id ?? 'N/A',
                    $itemName,
                    $purchaseFrom,
                    $date,
                    $purchasedBy,
                    number_format($amount, 2),
                    $currency,
                    $expense->paid_by ?? 'N/A',
                    ucfirst($expense->status ?? 'N/A'),
                    $type
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        $expenses = $this->getFilteredExpenses($request);
        
        // Enrich expenses with employee details for better display
        foreach ($expenses as $expense) {
            if (!empty($expense->employee_id)) {
                $employee = DB::table('allemployees')
                    ->select(
                        'employeeid',
                        DB::raw("CONCAT(firstname, ' ', lastname) as full_name")
                    )
                    ->where('employeeid', $expense->employee_id)
                    ->first();
                    
                if ($employee) {
                    $expense->employee_full_name = $employee->full_name;
                    $expense->employee_code = $employee->employeeid;
                }
            }
            
            // Set default currency if not set
            if (empty($expense->currency)) {
                $expense->currency = 'INR';
            }
        }

        $data = [
            'title' => 'Expense Report - ' . date('d-m-Y'),
            'expenses' => $expenses
        ];

        $pdf = PDF::loadView('hrms.hr.Reports.expense-reports.pdf', $data);
        
        return $pdf->download('expenses_report_' . date('d-m-Y') . '.pdf');
    }
}