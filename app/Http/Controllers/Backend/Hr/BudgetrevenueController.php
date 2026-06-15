<?php
namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
class BudgetrevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index()
{
    $branchId = getAdminBranchFilter();
    // Fetch all budget revenues
   $expenses = DB::table('budget-revenue')
        ->join('categories', 'budget-revenue.categories', '=', 'categories.id')
        ->join('subcategories', 'budget-revenue.sub-categories', '=', 'subcategories.id')
        ->select(
            'budget-revenue.*',
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

    return view('hrms.hr.accounting.budgets-revenue.index', compact('expenses', 'categories'));
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
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
            'img' => 'nullable|file|mimes:jpg,png,jpeg,pdf|max:2048',
        ]);
    
        // Handle file upload if exists
        $imagePath = null; // Initialize image path

        if ($request->hasFile('img')) {
            try {
                $profileImage = $request->file('img');
                $imageName = time() . '.' . $profileImage->getClientOriginalExtension();
                $profileImage->move(public_path('admin/assets/img/revenue/'), $imageName);

                // Store the complete path in the database
                $imagePath = 'uploads/revenue/' . $imageName;
            } catch (\Exception $e) {
                // Log the error message for debugging
                \Log::error('File upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'File upload error: ' . $e->getMessage());
            }
        }
        $branchId = Session::get('branch_id');
        // Insert the new budget revenue into the database
        DB::table('budget-revenue')->insert([
            'Notes' => $request->notes,
            'categories' => $request->category,
            'sub-categories' => $request->sub_category,
            'amount' => $request->amount,
            'img' => $imagePath, // Save the complete image path
            'revenue-date' => $request->expense_date,
            'branch_id' => $branchId, // store branch_id from session
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Redirect back with success message
        return redirect()->route('budgetrevenue.index')->with('success', 'Budget Revenue Created Successfully!');
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
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
            'img' => 'nullable|file|mimes:jpg,png,jpeg,pdf|max:2048',
        ]);

        // Handle file upload if exists
        $filename = null; // Initialize filename
        if ($request->hasFile('img')) {
            try {
                $filename = $request->file('img')->store('uploads/revenue', 'public');
            } catch (\Exception $e) {
                // Log the error message for debugging
                \Log::error('File upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'File upload error: ' . $e->getMessage());
            }
        }

        // Update the budget revenue in the database
        DB::table('budget-revenue')->where('id', $id)->update([
            'Notes' => $request->notes,
            'categories' => $request->category,
            'sub-categories' => $request->sub_category,
            'amount' => $request->amount,
            'img' => $filename ?: DB::table('budget-revenue')->where('id', $id)->value('img'), // Keep old file if no new file is uploaded
            'revenue-date' => $request->expense_date,
            'updated_at' => now(),
        ]);

        // Redirect back with success message
        return redirect()->route('budgetrevenue.index')->with('success', 'Budget Revenue Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete the budget revenue from the database
        DB::table('budget-revenue')->where('id', $id)->delete();

        // Redirect back with success message
        return redirect()->route('budgetrevenue.index')->with('success', 'Budget Revenue Deleted Successfully!');
    }
}
