<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $customerName = $request->get('customer_name');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $status = $request->get('status');

        $query = DB::table('expenses');

        if (!empty($customerName)) {
            $query->where('customer_name', 'like', '%' . $customerName . '%');
        }

        if (!empty($fromDate)) {
            try {
                $fromDateFormatted = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
                $query->where('departure_date', '>=', $fromDateFormatted);
            } catch (\Exception $e) {
                $fromDateFormatted = Carbon::parse($fromDate)->startOfDay();
                $query->where('departure_date', '>=', $fromDateFormatted);
            }
        }

        if (!empty($toDate)) {
            try {
                $toDateFormatted = Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
                $query->where('departure_date', '<=', $toDateFormatted);
            } catch (\Exception $e) {
                $toDateFormatted = Carbon::parse($toDate)->endOfDay();
                $query->where('departure_date', '<=', $toDateFormatted);
            }
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $expenses = $query->orderBy('created_at', 'desc')
                          ->paginate(10)
                          ->appends($request->query());

        // Get activity logs from expense_logs table
        $logs = DB::table('expense_logs')
            ->select(
                'expense_logs.id',
                'expense_logs.expense_id',
                'expense_logs.action',
                'expense_logs.user_id',
                'expense_logs.employee_id',
                'expense_logs.amount',
                'expense_logs.status',
                'expense_logs.details',
                'expense_logs.action_date'
            )
            ->orderBy('expense_logs.action_date', 'desc')
            ->limit(100)
            ->get();

        return view('hrms.hr.sales.expense.index', compact('expenses', 'logs'));
    }

    public function create()
    {
        $employees = DB::table('allemployees')
            ->select(
                'allemployees.employeeid as employee_code', 
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname, ' (', allemployees.employeeid, ')') AS full_name"),
                'department.department as department_name'
            )
            ->leftJoin('department', 'allemployees.department', '=', 'department.id')
            ->where('allemployees.status', 'active')
            ->get();
            
        $departments = DB::table('department')->pluck('department', 'department'); 

        return view('hrms.hr.sales.expense.create', compact('employees', 'departments'));
    }

    public function download($id)
    {
        $expense = DB::table('expenses')->where('expense_id', $id)->first();
        
        if (!$expense || !$expense->upload_receipt) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'File not found!');
            return redirect()->back();
        }

        $filePath = public_path($expense->upload_receipt);
        
        if (!file_exists($filePath)) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'File not found!');
            return redirect()->back();
        }

        $this->logExpenseAction($id, 'downloaded', 'Receipt file downloaded', $expense->employee_id, $expense->expense_amount);

        $fileName = pathinfo($expense->upload_receipt, PATHINFO_BASENAME);
        return response()->download($filePath, $fileName);
    }

    public function store(Request $request)
    {
        $branchId = Session::get('branch_id');
        $Id = $this->generateID('EXP');

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|max:100',
            'department_name' => 'nullable|string|max:100',
            'departure_date' => 'required|date',
            'arrival_date' => 'nullable|date|after_or_equal:departure_date',
            'purpose_of_visit' => 'required|string|max:1000',
            'place_of_visit' => 'nullable|string|max:255',
            'duration_days' => 'nullable|integer|min:1',
            'customer_name' => 'nullable|string|max:255',
            'is_billable' => 'required|boolean',
            'expense_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'upload_receipt' => 'required|file|mimes:png,jpg,jpeg,gif,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            Session::flash('messageType', 'error');
            Session::flash('message', 'Validation Error!');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $filePath = null;
        if ($request->hasFile('upload_receipt')) {
            $file = $request->file('upload_receipt');
            $extension = $file->getClientOriginalExtension();
            $timestamp = time();
            $fileName = "expense_{$timestamp}.{$extension}";
            $destinationPath = 'uploads/expenses';
            $filePath = $file->move($destinationPath, $fileName);
        }

        $data = [
            'expense_id' => $Id,
            'employee_id' => $request->input('employee_id'),
            'department_name' => $request->input('department_name'),
            'departure_date' => Carbon::parse($request->input('departure_date')),
            'arrival_date' => $request->input('arrival_date') ? Carbon::parse($request->input('arrival_date')) : null,
            'purpose_of_visit' => $request->input('purpose_of_visit'),
            'place_of_visit' => $request->input('place_of_visit'),
            'duration_days' => $request->input('duration_days'),
            'customer_name' => $request->input('customer_name'),
            'is_billable' => $request->input('is_billable'),
            'expense_amount' => $request->input('expense_amount'),
            'currency' => $request->input('currency'),
            'upload_receipt' => $filePath,
            'status' => 'pending',
            'branch_id' => $branchId,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert the expense
        DB::table('expenses')->insert($data);

        // Log the creation - FIXED: Added status parameter
        $this->logExpenseAction($Id, 'created', 'Travel expense claim submitted', $request->input('employee_id'), $request->input('expense_amount'), 'pending');

        Session::flash('messageType', 'success');
        Session::flash('message', 'Travel expense claim submitted successfully!');
        return redirect()->route('expense.index');
    }

    public function edit($id)
{
    $expense = DB::table('expenses')->where('expense_id', $id)->first();
    
    if (!$expense) {
        Session::flash('messageType', 'error');
        Session::flash('message', 'Expense not found!');
        return redirect()->route('expense.index');
    }

    $employees = DB::table('allemployees')
        ->select(
            'allemployees.employeeid as employee_code',
            DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname, ' (', allemployees.employeeid, ')') AS full_name"),
            'department.department as department_name'
        )
        ->leftJoin('department', 'allemployees.department', '=', 'department.id')
        ->where('allemployees.status', 'active')
        ->get();
    
    $departments = DB::table('department')->pluck('department', 'department'); 

    // Fix: Format dates to match datetimepicker format (DD-MMM-YYYY)
    $expense->formatted_departure_date = $expense->departure_date 
        ? Carbon::parse($expense->departure_date)->format('d-M-Y') : '';
    $expense->formatted_arrival_date = $expense->arrival_date 
        ? Carbon::parse($expense->arrival_date)->format('d-M-Y') : '';

    return view('hrms.hr.sales.expense.edit', compact('expense', 'employees', 'departments'));
}

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|max:100',
            'department_name' => 'nullable|string|max:100',
            'departure_date' => 'required|date',
            'arrival_date' => 'nullable|date|after_or_equal:departure_date',
            'purpose_of_visit' => 'required|string|max:1000',
            'place_of_visit' => 'nullable|string|max:255',
            'duration_days' => 'nullable|integer|min:1',
            'customer_name' => 'nullable|string|max:255',
            'is_billable' => 'required|boolean',
            'expense_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'upload_receipt' => 'nullable|file|mimes:png,jpg,jpeg,gif,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('messageType', 'error')
                ->with('message', 'Validation Error!');
        }

        $expense = DB::table('expenses')->where('expense_id', $id)->first();
        
        if (!$expense) {
            return redirect()->route('expense.index')
                ->with('messageType', 'error')
                ->with('message', 'Expense not found!');
        }

        $filePath = $expense->upload_receipt;
        if ($request->hasFile('upload_receipt')) {
            if ($filePath && file_exists(public_path($filePath))) {
                unlink(public_path($filePath));
            }
            
            $file = $request->file('upload_receipt');
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'.'.$extension;
            $destinationPath = 'uploads/expenses';
            
            // FIX: Store the path correctly
            $file->move(public_path($destinationPath), $fileName);
            $filePath = $destinationPath . '/' . $fileName;
        }

        // FIX: Use Carbon::parse() instead of createFromFormat
        $data = [
            'employee_id' => $request->input('employee_id'),
            'department_name' => $request->input('department_name'),
            'departure_date' => Carbon::parse($request->input('departure_date')),
            'arrival_date' => $request->input('arrival_date') ? Carbon::parse($request->input('arrival_date')) : null,
            'purpose_of_visit' => $request->input('purpose_of_visit'),
            'place_of_visit' => $request->input('place_of_visit'),
            'duration_days' => $request->input('duration_days'),
            'customer_name' => $request->input('customer_name'),
            'is_billable' => $request->input('is_billable'),
            'expense_amount' => $request->input('expense_amount'),
            'currency' => $request->input('currency'),
            'upload_receipt' => $filePath,
            'updated_at' => now(),
        ];

        DB::table('expenses')->where('expense_id', $id)->update($data);

        // Log the update
        $this->logExpenseAction($id, 'updated', 'Expense details updated', $request->input('employee_id'), $request->input('expense_amount'), $expense->status);
        
        return redirect()->route('expense.index')
            ->with('messageType', 'success')
            ->with('message', 'Expense updated successfully!');
    }

    public function destroy($id)
    {
        $expense = DB::table('expenses')->where('expense_id', $id)->first();
        
        if (!$expense) {
            return response(['status' => 'error', 'message' => 'Expense not found!']);
        }

        if ($expense->upload_receipt && file_exists(public_path($expense->upload_receipt))) {
            unlink(public_path($expense->upload_receipt));
        }

        // Log the deletion
        $this->logExpenseAction($id, 'deleted', "Expense '{$id}' deleted", $expense->employee_id, $expense->expense_amount, $expense->status);

        DB::table('expenses')->where('expense_id', $id)->delete();
        return response(['status' => 'success', 'message' => 'Expense deleted successfully!', 'id' => $id]);
    }

    private function generateID($base)
    {
        $lastID = DB::table('expenses')
            ->whereNotNull('expense_id')
            ->orderBy('id', 'desc')
            ->value('expense_id');

        $baseID = Str::upper($base).'-';
        $newIDNumber = $lastID ? (int) substr($lastID, 4) + 1 : 1;

        do {
            $newID = $baseID . str_pad($newIDNumber, 4, '0', STR_PAD_LEFT);
            $newIDNumber++;
        } while (DB::table('expenses')->where('expense_id', $newID)->exists());

        return $newID;
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $expense = DB::table('expenses')->where('expense_id', $id)->first();
            
            if (!$expense) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Expense not found!'
                ], 404);
            }

            $validStatuses = ['pending', 'approved', 'rejected'];
            $newStatus = $request->status;
            
            if (!in_array($newStatus, $validStatuses)) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Invalid status value!'
                ], 400);
            }

            if ($expense->status === $newStatus) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Expense is already ' . $newStatus . '!'
                ], 400);
            }

            $statusMessage = "Status changed from " . ucfirst($expense->status) . " to " . ucfirst($newStatus);
            
            $updated = DB::table('expenses')->where('expense_id', $id)->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

            if (!$updated) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Failed to update expense status!'
                ], 500);
            }

            $logSuccess = $this->logExpenseAction($id, 'status_changed', $statusMessage, $expense->employee_id, $expense->expense_amount, $newStatus);

            return response()->json([
                'status' => 'success', 
                'message' => 'Expense status has been updated to ' . $newStatus . '!',
                'new_status' => $newStatus,
                'logged' => $logSuccess
            ]);

        } catch (\Exception $e) {
            Log::error("Error changing expense status: " . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function logExpenseAction($expenseId, $action, $details = null, $employeeId = null, $amount = null, $status = null)
    {
        try {
            Log::info("Attempting to log expense action: {$action} for expense ID: {$expenseId}");
            
            $logData = [
                'expense_id' => $expenseId,
                'action' => $action,
                'user_id' => Auth::id() ?? session('user_id') ?? 1,
                'employee_id' => $employeeId,
                'amount' => $amount,
                'status' => $status,
                'details' => $details,
                'action_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ];

            Log::info('Log data to insert:', $logData);

            $inserted = DB::table('expense_logs')->insert($logData);

            if ($inserted) {
                Log::info("✅ Expense action logged successfully: Expense ID: {$expenseId}, Action: {$action}");
                return true;
            } else {
                Log::error("❌ Failed to insert expense log for Expense ID: {$expenseId}");
                return false;
            }

        } catch (\Exception $e) {
            Log::error("❌ Error logging expense action: " . $e->getMessage());
            Log::error("❌ Stack trace: " . $e->getTraceAsString());
            
            try {
                $minimalLogData = [
                    'expense_id' => $expenseId,
                    'action' => $action,
                    'details' => $details ?: 'Action performed',
                    'action_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                DB::table('expense_logs')->insert($minimalLogData);
                Log::info("✅ Minimal log inserted for expense ID: {$expenseId}");
                return true;
            } catch (\Exception $e2) {
                Log::error("❌ Even minimal log failed: " . $e2->getMessage());
                return false;
            }
        }
    }

    public function testLogCreation(Request $request)
    {
        $expenseId = $request->get('expense_id', 'EXP-0001');
        $action = $request->get('action', 'test');
        $employeeId = $request->get('employee_id', 'AP-00067');
        $amount = $request->get('amount', 100.00);
        
        $result = $this->logExpenseAction($expenseId, $action, 'Test log creation', $employeeId, $amount, 'pending');
        
        $logs = DB::table('expense_logs')->get();
        
        return response()->json([
            'message' => 'Test completed',
            'log_creation_success' => $result,
            'logs_count' => $logs->count(),
            'recent_logs' => DB::table('expense_logs')->orderBy('id', 'desc')->limit(5)->get()
        ]);
    }

    public function viewLogs()
    {
        $logs = DB::table('expense_logs')
            ->orderBy('action_date', 'desc')
            ->limit(50)
            ->get();
            
        return response()->json([
            'total_logs' => DB::table('expense_logs')->count(),
            'logs' => $logs
        ]);
    }
}