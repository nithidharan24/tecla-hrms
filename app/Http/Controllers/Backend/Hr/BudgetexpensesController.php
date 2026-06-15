<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class BudgetexpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index()
{
    $branchId = getAdminBranchFilter();
    $expenses = DB::table('budgets-expenses')
        ->join('categories', 'budgets-expenses.categories', '=', 'categories.id')
        ->join('subcategories', 'budgets-expenses.sub-categories', '=', 'subcategories.id')
        ->select(
            'budgets-expenses.*',
            'categories.name as category_name',
            'subcategories.name as subcategory_name'
        )
        ->get();
        if ($branchId) {
            $expenses->where('e.branch_id', $branchId);
        }
    // Fetch categories with their subcategories
    $categories = DB::table('categories')
        ->leftJoin('subcategories', 'categories.id', '=', 'subcategories.category_id')
        ->select(
            'categories.id as category_id',
            'categories.name as category_name',
            'subcategories.id as subcategory_id',
            'subcategories.name as subcategory_name'
        )
        ->get()
        ->groupBy('category_id');

    return view('hrms.hr.accounting.budgets-expenses.index', compact('expenses', 'categories'));
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Fetch categories from the database
        $categories = DB::table('categories')->get();

        // Pass the categories to the view
        return view('hrms.hr.accounting.budgets-expenses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the input data
        $request->validate([
            'notes' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'sub_category' => 'required|string|max:255',
            'amount' => 'required',
            'expense_date' => 'required|date',
            'img' => 'nullable|file|mimes:jpg,png,jpeg,pdf|max:2048', // Validate file
        ]);

        // Handle file upload if exists
        if ($request->hasFile('img')) {
            $profileImage = $request->file('img');
            $imageName = time() . '.' . $profileImage->getClientOriginalExtension();
            $profileImage->move(public_path('uploads/expenses'), $imageName);
            
            // Store the complete path in the database
            $filename = 'uploads/expenses/' . $imageName;
        } else {
            $filename = null; // If no file is uploaded, set it to null
        }
        $branchId = Session::get('branch_id');
        // Insert the new budget expense into the database
        DB::table('budgets-expenses')->insert([
            'Notes' => $request->notes,
            'categories' => $request->category,
            'sub-categories' => $request->sub_category,
            'amount' => $request->amount,
            'currency_symbol' => $request->currency_symbol, // Save the currency symbol
            'img' => $filename, // Save the file name if exists
            'expense-date' => $request->expense_date,
            'branch_id' => $branchId, // store branch_id from session
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect back with success message
        return redirect()->route('budgetexpenses.index')->with('success', 'Budget Expense Created Successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Fetch the budget expense by ID using DB facade
        $expense = DB::table('budgets-expenses')->where('id', $id)->first();

        // Fetch categories from the database
        $categories = DB::table('categories')->get();

        // Check if expense exists
        if (!$expense) {
            return redirect()->route('budgetexpenses.index')->with('error', 'Expense not found!');
        }

        // Return the view for editing the expense
        return view('hrms.hr.accounting.budget_expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'notes' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'sub_category' => 'required|string|max:255',
            'amount' => 'required',
            'expense_date' => 'required|date',
            'img' => 'nullable|file|mimes:jpg,png,jpeg,pdf|max:2048', // Validate file
        ]);

        // Handle file upload if exists
        if ($request->hasFile('img')) {
            $profileImage = $request->file('img');
            $imageName = time() . '.' . $profileImage->getClientOriginalExtension();
            $profileImage->move(public_path('uploads/expenses'), $imageName);
            
            // Store the complete path in the database
            $filename = 'uploads/expenses/' . $imageName;
        } else {
            $filename = DB::table('budgets-expenses')->where('id', $id)->value('img'); // Keep the old file
        }

        // Update the budget expense in the database
        DB::table('budgets-expenses')->where('id', $id)->update([
            'Notes' => $request->notes,
            'categories' => $request->category,
            'sub-categories' => $request->sub_category,
            'amount' => $request->amount,
            'currency_symbol' => $request->currency_symbol, // Update the currency symbol
            'img' => $filename,
            'expense-date' => $request->expense_date,
            'updated_at' => now(),
        ]);

        // Redirect back with success message
        return redirect()->route('budgetexpenses.index')->with('success', 'Budget Expense Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check if the budget expense exists
        $expense = DB::table('budgets-expenses')->where('id', $id)->first();

        if (!$expense) {
            return redirect()->route('budgetexpenses.index')->with('error', 'Expense not found!');
        }

        // Delete the expense using DB facade
        DB::table('budgets-expenses')->where('id', $id)->delete();

        // Redirect back with success message
        return redirect()->route('budgetexpenses.index')->with('success', 'Budget Expense Deleted Successfully!');
    }
}
