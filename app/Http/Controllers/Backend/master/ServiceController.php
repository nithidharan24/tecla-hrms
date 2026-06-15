<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = DB::table('services')
        ->orderBy('name', 'asc') // alphabetical A → Z
        ->get();
        return view('hrms.master.service.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'remark' => 'nullable|string'
        ]);

        DB::table('services')->insert([
            'name' => $validated['name'],
            'remark' => $validated['remark'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Service added successfully!']);
        }

        return redirect()->back()->with('success', 'Service added successfully!');
    }

    public function edit($id)
    {
        $service = DB::table('services')->where('id', $id)->first();
        
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }
        
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'remark' => 'nullable|string'
        ]);

        $updated = DB::table('services')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
                'remark' => $validated['remark'],
                'updated_at' => now()
            ]);

        if (!$updated) {
            return response()->json(['error' => 'Service not found or no changes made'], 404);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Service updated successfully!']);
        }

        return redirect()->back()->with('success', 'Service updated successfully!');
    }

    public function destroy($id)
    {
        $deleted = DB::table('services')->where('id', $id)->delete();
        
        if (!$deleted) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Service deleted successfully!']);
        }

        return redirect()->back()->with('success', 'Service deleted successfully!');
    }
}