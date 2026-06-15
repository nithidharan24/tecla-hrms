<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class CategoriesController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')->get();
        $subcategories = DB::table('subcategories')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->select('subcategories.*', 'categories.name as category_name')
            ->get();
            
        return view('hrms.hr.accounting.categories.index', compact('categories', 'subcategories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        DB::table('categories')->insert([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }

    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:subcategories,name',
        ]);

        DB::table('subcategories')->insert([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Subcategory added successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id,
        ]);

        DB::table('categories')->where('id', $id)->update([
            'name' => $request->name,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function updateSubcategory(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:subcategories,name,'.$id,
        ]);

        DB::table('subcategories')->where('id', $id)->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Subcategory updated successfully!');
    }

    public function destroyCategory($id)
    {
        DB::beginTransaction();
        try {
            DB::table('subcategories')->where('category_id', $id)->delete();
            DB::table('categories')->where('id', $id)->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Category and its subcategories deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to delete category!');
        }
    }

    public function destroySubcategory($id)
    {
        DB::table('subcategories')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Subcategory deleted successfully!');
    }
}