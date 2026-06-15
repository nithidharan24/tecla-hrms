<?php
namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TrainingTypeController extends Controller
{
    public function index() {
        $trainingTypes = DB::table('training_types')->get();
        return view('hrms.performance.Training.Training-type.index', compact('trainingTypes'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);
        
        DB::table('training_types')->insert([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Training type added successfully');
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        DB::table('training_types')->where('id', $id)->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Training type updated successfully');
    }

    public function destroy($id) {
        try {
            $deleted = DB::table('training_types')->where('id', $id)->delete();
            
            if ($deleted) {
                if(request()->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Training type deleted successfully']);
                }
                return redirect()->back()->with('success', 'Training type deleted successfully');
            } else {
                if(request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Training type not found'], 404);
                }
                return redirect()->back()->with('error', 'Training type not found');
            }
        } catch (\Exception $e) {
            if(request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error deleting training type'], 500);
            }
            return redirect()->back()->with('error', 'Error deleting training type');
        }
    }

    public function changeStatus($id) {
        $type = DB::table('training_types')->find($id);
        $newStatus = ($type->status == 'Active') ? 'Inactive' : 'Active';
        
        DB::table('training_types')->where('id', $id)->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Training type status updated');
    }
}