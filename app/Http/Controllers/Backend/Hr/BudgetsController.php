<?php
namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class BudgetsController extends Controller
{
 public function index(Request $request)
{
    $branchId = getAdminBranchFilter();
    
    $query = DB::table('budgets');
    
    if ($branchId) {
        $query->where('branch_id', $branchId);
    }
    
    // Apply search filter
    if ($request->filled('search')) {
        $query->where('budget_title', 'like', '%' . $request->search . '%');
    }
    
    // Apply budget type filter
    if ($request->filled('budget_type')) {
        $query->where('budget_type', $request->budget_type);
    }
    
    // Apply date range filter
    if ($request->filled('date_range')) {
        $today = Carbon::today();
        
        switch ($request->date_range) {
            case 'today':
                $query->whereDate('start_date', $today);
                break;
            case 'this_week':
                $query->whereBetween('start_date', [
                    $today->startOfWeek()->toDateString(),
                    $today->endOfWeek()->toDateString()
                ]);
                break;
            case 'this_month':
                $query->whereYear('start_date', $today->year)
                      ->whereMonth('start_date', $today->month);
                break;
            case 'this_year':
                $query->whereYear('start_date', $today->year);
                break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $query->whereBetween('start_date', [
                        $request->start_date, 
                        $request->end_date
                    ]);
                }
                break;
        }
    }
    
    $budgets = $query->orderBy('created_at', 'desc')->get();
    $budgetLogs = DB::table('budget_activity_logs')
    ->orderBy('action_date', 'desc')
    ->limit(200)
    ->get();
    
    return view('hrms.hr.accounting.budgets.index', compact('budgets','budgetLogs'));
}

    public function create()
    {
        return view('hrms.hr.accounting.budgets.create');
    }

  public function store(Request $request)
{
    $request->validate([
        'budget_title' => 'required|string|max:255',
        'budget_type' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'revenue_title' => 'required|array|min:1',
        'revenue_amount' => 'required|array|min:1',
        'expenses_title' => 'required|array|min:1',
        'expenses_amount' => 'required|array|min:1',
        'tax_amount' => 'nullable|numeric',
    ]);

    $budgetData = $this->calculateBudgetData($request);
    $branchId = Session::get('branch_id');

    $data = [
        'budget_title' => $request->budget_title,
        'budget_type' => $request->budget_type,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'revenue_title' => json_encode($request->revenue_title),
        'revenue_amount' => json_encode($request->revenue_amount),
        'total_revenue' => $budgetData['total_revenue'],
        'expenses_title' => json_encode($request->expenses_title),
        'expenses_amount' => json_encode($request->expenses_amount),
        'total_expenses' => $budgetData['total_expenses'],
        'expected_profit' => $budgetData['expected_profit'],
        'tax_amount' => $budgetData['tax_amount'],
        'budget_amount' => $budgetData['budget_amount'],
        'branch_id' => $branchId,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    $budgetId = DB::table('budgets')->insertGetId($data);

    // LOG
    $this->logBudgetAction(
        $budgetId,
        'created',
        "Budget '{$request->budget_title}' created.",
        $request->budget_title,
        $budgetData['total_revenue'],
        $budgetData['total_expenses'],
        $budgetData['expected_profit'],
        $budgetData['budget_amount']
    );

    return redirect()->route('budgets.index')->with('success', 'Budget added successfully.');
}


public function edit($id)
{
    // Find the budget by its ID
    $budget = DB::table('budgets')->where('id', $id)->first();

    // Check if the budget exists
    if (!$budget) {
        return redirect()->route('budgets.index')->with('error', 'Budget not found.');
    }

    // Instead of decoding JSON, directly assign arrays
    $budget->revenue_title = $budget->revenue_title ? $budget->revenue_title : [];
    $budget->revenue_amount = $budget->revenue_amount ? $budget->revenue_amount : [];
    $budget->expenses_title = $budget->expenses_title ? $budget->expenses_title : [];
    $budget->expenses_amount = $budget->expenses_amount ? $budget->expenses_amount : [];

    // Return the edit view with the budget data
    return view('hrms.hr.accounting.budgets.edit', compact('budget'));
}

    
    

   public function update(Request $request, $id)
{
    $old = DB::table('budgets')->where('id', $id)->first();

    if (!$old) {
        return redirect()->route('budgets.index')->with('error', 'Budget not found.');
    }

    $budgetData = $this->calculateBudgetData($request);

    $data = [
        'budget_title' => $request->budget_title,
        'budget_type' => $request->budget_type,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'revenue_title' => json_encode($request->revenue_title),
        'revenue_amount' => json_encode($request->revenue_amount),
        'total_revenue' => $budgetData['total_revenue'],
        'expenses_title' => json_encode($request->expenses_title),
        'expenses_amount' => json_encode($request->expenses_amount),
        'total_expenses' => $budgetData['total_expenses'],
        'expected_profit' => $budgetData['expected_profit'],
        'tax_amount' => $budgetData['tax_amount'],
        'budget_amount' => $budgetData['budget_amount'],
        'updated_at' => now(),
    ];

    DB::table('budgets')->where('id', $id)->update($data);

    // LOG
    $this->logBudgetAction(
        $id,
        'updated',
        "Budget '{$request->budget_title}' updated.",
        $request->budget_title,
        $budgetData['total_revenue'],
        $budgetData['total_expenses'],
        $budgetData['expected_profit'],
        $budgetData['budget_amount']
    );

    return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
}

    

    public function destroy($id)
{
    $budget = DB::table('budgets')->where('id', $id)->first();

    if (!$budget) {
        return redirect()
            ->route('budgets.index')
            ->with('error', 'Budget not found.');
    }

    // Delete the record
    DB::table('budgets')->where('id', $id)->delete();

    // Log activity
    $this->logBudgetAction(
        $id,
        'deleted',
        "Budget '{$budget->budget_title}' deleted.",
        $budget->budget_title,
        $budget->total_revenue,
        $budget->total_expenses,
        $budget->expected_profit,
        $budget->budget_amount
    );

    return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget deleted successfully.');
}


    private function calculateBudgetData(Request $request)
    {
        // Ensure inputs are arrays
        $revenueAmounts = $request->revenue_amount ?? [];
        $expenseAmounts = $request->expenses_amount ?? [];

        // Calculate totals
        $totalRevenue = array_sum($revenueAmounts); // A: Sum of revenue amounts
        $totalExpenses = array_sum($expenseAmounts); // B: Sum of expenses amounts
        $taxAmount = $request->tax_amount ?? 0; // D: Tax Amount (set to 0 if not provided)

        // Calculate expected profit (C = A - B)
        $expectedProfit = $totalRevenue - $totalExpenses;

        // Calculate budget amount (E = C - D)
        $budgetAmount = $expectedProfit - $taxAmount;

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'expected_profit' => $expectedProfit,
            'tax_amount' => $taxAmount,
            'budget_amount' => $budgetAmount,
        ];
    }

    private function logBudgetAction($budgetId, $action, $details, $title, $totalRevenue, $totalExpenses, $expectedProfit, $budgetAmount)
{
    DB::table('budget_activity_logs')->insert([
        'budget_id'      => $budgetId,
        'action'         => $action,
        'title'          => $title,
        'total_revenue'  => $totalRevenue,
        'total_expenses' => $totalExpenses,
        'expected_profit'=> $expectedProfit,
        'budget_amount'  => $budgetAmount,
        'details'        => $details,
        'user_id'        => auth()->id() ?? 1,
        'user_name'      => auth()->user()->name ?? 'System',
        'ip_address'     => request()->ip(),
        'user_agent'     => request()->userAgent(),
        'action_date'    => now(),
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);
}


   
}
