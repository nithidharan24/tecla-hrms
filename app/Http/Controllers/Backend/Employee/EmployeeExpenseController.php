<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class EmployeeExpenseController extends Controller
{
 public function index()
{
    $role = Session::get('role');
    $currentMonth = now()->format('Y-m');
    $expenses = [];
    $expenseSummary = [];

    if ($role === 'employee') {
        $employeeId = Session::get('user_id');

        $expenses = DB::table('employee_expenses')
                    ->where('employee_id', $employeeId)
                    ->orderBy('expense_date', 'desc')
                    ->get();

        $expenseSummary = [
            'total' => DB::table('employee_expenses')
                        ->where('employee_id', $employeeId)
                        ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$currentMonth])
                        ->sum('expense_amount'),
            'approved' => DB::table('employee_expenses')
                        ->where('employee_id', $employeeId)
                        ->where('expense_status', 'approved')
                        ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$currentMonth])
                        ->count(),
            'pending' => DB::table('employee_expenses')
                        ->where('employee_id', $employeeId)
                        ->where('expense_status', 'pending')
                        ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$currentMonth])
                        ->count(),
            'rejected' => DB::table('employee_expenses')
                        ->where('employee_id', $employeeId)
                        ->where('expense_status', 'rejected')
                        ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$currentMonth])
                        ->count(),
        ];
    } elseif ($role === 'admin') {
        $expenses = DB::table('employee_expenses')
                    ->join('allemployees', 'employee_expenses.employee_id', '=', 'allemployees.id')
                    ->select('employee_expenses.*', 'allemployees.firstname', 'allemployees.lastname')
                    ->orderBy('expense_date', 'desc')
                    ->get();

        $expenseSummary = [
            'total' => DB::table('employee_expenses')
                        ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$currentMonth])
                        ->sum('expense_amount'),
            'approved' => DB::table('employee_expenses')
                        ->where('expense_status', 'approved')
                        ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$currentMonth])
                        ->count(),
            'pending' => DB::table('employee_expenses')
                        ->where('expense_status', 'pending')
                        ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$currentMonth])
                        ->count(),
            'rejected' => DB::table('employee_expenses')
                        ->where('expense_status', 'rejected')
                        ->whereRaw("DATE_FORMAT(expense_date, '%Y-%m') = ?", [$currentMonth])
                        ->count(),
        ];
    } else {
        abort(403, 'Unauthorized');
    }

    return view('hrms.Employee.emp_expense.index', [
        'expenses' => $expenses,
        'expenseSummary' => $expenseSummary
    ]);
}


    public function create()
    {
        return view('hrms.Employee.emp_expense.create');
    }
public function updateStatus(Request $request, $id)
{
    try {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        $expense = DB::table('employee_expenses')->where('id', $id)->first();

        if (!$expense) {
            return response()->json(['error' => 'Expense not found'], 404);
        }

        $updated = DB::table('employee_expenses')
            ->where('id', $id)
            ->update([
                'expense_status' => $validated['status'],
                'approved_by' => Session::get('user_id'),
                'updated_at' => now()
            ]);

        if ($updated) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Update failed'], 500);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString() // Only for development
        ], 500);
    }
}
   public function store(Request $request)
{
    $employeeId = Session::get('user_id');
    $filePath = null;

    if ($request->hasFile('receipt_attachment')) {
        $file = $request->file('receipt_attachment');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('employee_expenses/receipts');
        $file->move($destinationPath, $filename);
        $filePath = 'employee_expenses/receipts/' . $filename;
    }

    DB::table('employee_expenses')->insert([
        'employee_id' => $employeeId,
        'expense_amount' => $request->expense_amount,
        'expense_purpose' => $request->expense_purpose,
        'receipt_attachment' => $filePath,
        'expense_date' => $request->expense_date,
        'expense_status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('employee_expenses.index')->with('success', 'Expense submitted successfully!');
}


    public function show($id)
    {
        $expense = DB::table('employee_expenses')->where('id', $id)->first();
        if (!$expense) abort(404);
        return view('hrms.Employee.emp_expense.show', ['expense' => $expense]);
    }

    public function destroy($id)
    {
        $expense = DB::table('employee_expenses')
                    ->where('id', $id)
                    ->where('expense_status', 'pending')
                    ->first();

        if (!$expense) {
            return redirect()->route('employee_expenses.index')->with('error', 'Expense cannot be deleted');
        }

        if ($expense->receipt_attachment) {
            Storage::disk('public')->delete($expense->receipt_attachment);
        }

        DB::table('employee_expenses')->where('id', $id)->delete();

        return redirect()->route('employee_expenses.index')->with('success', 'Expense deleted successfully');
    }

    public function pendingExpenses()
    {
        $this->authorizeAdmin();

        $expenses = DB::table('employee_expenses')
                    ->join('allemployees', 'employee_expenses.employee_id', '=', 'allemployees.id')
                    ->select('employee_expenses.*', 'allemployees.firstname', 'allemployees.lastname')
                    ->where('expense_status', 'pending')
                    ->orderBy('expense_date', 'asc')
                    ->get();

        return view('hrms.Employee.emp_expense.pending', ['expenses' => $expenses]);
    }
public function edit($id)
{
    $expense = DB::table('employee_expenses')->where('id', $id)->first();
    
    if (!$expense) {
        abort(404);
    }
    
    // Check if the user is authorized to edit this expense
    if (Session::get('role') === 'employee' && $expense->employee_id !== Session::get('user_id')) {
        abort(403, 'Unauthorized');
    }
    
    return view('hrms.Employee.emp_expense.edit', ['expense' => $expense]);
}

public function update(Request $request, $id)
{
    $expense = DB::table('employee_expenses')->where('id', $id)->first();
    
    if (!$expense) {
        abort(404);
    }
    
    // Check if the user is authorized to update this expense
    if (Session::get('role') === 'employee' && $expense->employee_id !== Session::get('user_id')) {
        abort(403, 'Unauthorized');
    }
    
    // Only allow updates for pending expenses
    if ($expense->expense_status !== 'pending') {
        return redirect()->route('employee_expenses.index')
            ->with('error', 'Only pending expenses can be updated');
    }
    
    $validated = $request->validate([
        'expense_date' => 'required|date',
        'expense_amount' => 'required|numeric|min:0',
        'expense_purpose' => 'required|string|max:500',
        'receipt_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);
    
    $updateData = [
        'expense_date' => $validated['expense_date'],
        'expense_amount' => $validated['expense_amount'],
        'expense_purpose' => $validated['expense_purpose'],
        'updated_at' => now(),
    ];
    
    if ($request->hasFile('receipt_attachment')) {
        // Delete old file if exists
        if ($expense->receipt_attachment) {
            Storage::disk('public')->delete($expense->receipt_attachment);
        }
        
        // Store new file
        $file = $request->file('receipt_attachment');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('employee_expenses/receipts');
        $file->move($destinationPath, $filename);
        $updateData['receipt_attachment'] = 'employee_expenses/receipts/' . $filename;
    }
    
    DB::table('employee_expenses')
        ->where('id', $id)
        ->update($updateData);
    
    return redirect()->route('employee_expenses.index')
        ->with('success', 'Expense updated successfully');
}
    public function approve($id)
    {
        $this->authorizeAdmin();

        DB::table('employee_expenses')
            ->where('id', $id)
            ->update(['expense_status' => 'approved', 'updated_at' => now()]);

        return redirect()->back()->with('success', 'Expense approved.');
    }

    public function reject($id)
    {
        $this->authorizeAdmin();

        DB::table('employee_expenses')
            ->where('id', $id)
            ->update(['expense_status' => 'rejected', 'updated_at' => now()]);

        return redirect()->back()->with('success', 'Expense rejected.');
    }

    private function authorizeAdmin()
    {
        if (Session::get('role') !== 'admin') {
            abort(403, 'Unauthorized action');
        }
    }
}
