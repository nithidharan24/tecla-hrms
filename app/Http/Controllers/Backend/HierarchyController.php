<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HierarchyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Get all hierarchy entries
            $hierarchies = collect(DB::table('hierarchies')->orderBy('created_at', 'desc')->get());
            
            return view('hrms.Employee.hierarchy.index', compact('hierarchies'));
        } catch (\Exception $e) {
            Log::error('Error fetching hierarchies: ' . $e->getMessage());
            return view('hrms.Employee.hierarchy.index', ['hierarchies' => collect()]);
        }
    }

    /**
     * Export hierarchy data
     */
    public function export()
    {
        try {
            $hierarchies = DB::table('hierarchies')->orderBy('created_at', 'desc')->get();
            
            // You can implement CSV, Excel, or PDF export here
            // For now, returning JSON
            return response()->json([
                'success' => true,
                'data' => $hierarchies
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting hierarchies: ' . $e->getMessage());
            return response()->json(['error' => 'Error exporting data'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hrms.Employee.hierarchy.create');
    }

    /**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    $request->validate([
        'hierarchy_level' => 'required|string|max:255',
        'modules' => 'array',
    ]);

    try {
        $modules = [];
        foreach ($request->modules ?? [] as $module => $permissions) {
            if (isset($permissions['enabled'])) {
                $modules[$module] = [
                    'view'     => !empty($permissions['view']),
                    'create'   => !empty($permissions['create']),
                    'edit'     => !empty($permissions['edit']),
                    'delete'   => !empty($permissions['delete']),
                    'approve'  => !empty($permissions['approve']),
                    'download' => !empty($permissions['download']),
                    'export'   => !empty($permissions['export']),
                ];
            }
        }

        DB::table('hierarchies')->insert([
            'hierarchy_level' => $request->hierarchy_level,
            'modules' => json_encode($modules),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('hierarchy.index')->with('success', 'Hierarchy level created successfully!');
    } catch (\Exception $e) {
        Log::error('Error creating hierarchy: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Error creating hierarchy. Please try again.');
    }
}
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $hierarchy = DB::table('hierarchies')->where('id', $id)->first();
            
            if (!$hierarchy) {
                return redirect()->route('hierarchy.index')->with('error', 'Hierarchy record not found');
            }
            
            // Decode JSON modules safely
            try {
                $hierarchy->modules = json_decode($hierarchy->modules ?? '[]', true);
                if (!is_array($hierarchy->modules)) {
                    $hierarchy->modules = [];
                }
            } catch (\Exception $e) {
                $hierarchy->modules = [];
            }
            
            return view('hrms.Employee.hierarchy.show', compact('hierarchy'));
        } catch (\Exception $e) {
            Log::error('Error showing hierarchy: ' . $e->getMessage());
            return redirect()->route('hierarchy.index')->with('error', 'Error loading hierarchy details.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) 
    {
        try {
            $hierarchy = DB::table('hierarchies')->where('id', $id)->first();
            
            if (!$hierarchy) {
                return redirect()->route('hierarchy.index')->with('error', 'Hierarchy record not found');
            }

            // Decode JSON properly (associative array with module => permissions)
            $hierarchy->modules = json_decode($hierarchy->modules ?? '{}', true);
            if (!is_array($hierarchy->modules)) {
                $hierarchy->modules = [];
            }

            return view('hrms.Employee.hierarchy.edit', compact('hierarchy'));

        } catch (\Exception $e) {
            Log::error('Error editing hierarchy: ' . $e->getMessage());
            return redirect()->route('hierarchy.index')->with('error', 'Error loading hierarchy for editing.');
        }
    }
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'hierarchy_level' => 'required|string|max:255',
                'modules' => 'array',
            ]);

            // Fetch hierarchy record
            $hierarchy = DB::table('hierarchies')->where('id', $id)->first();
            if (!$hierarchy) {
                return redirect()->route('hierarchy.index')->with('error', 'Hierarchy record not found');
            }
        
            $modules = [];
            foreach ($request->modules ?? [] as $module => $permissions) {
                if (isset($permissions['enabled'])) {
                    $modules[$module] = [
                        'view'     => !empty($permissions['view']),
                        'create'   => !empty($permissions['create']),
                        'edit'     => !empty($permissions['edit']),
                        'delete'   => !empty($permissions['delete']),
                        'approve'  => !empty($permissions['approve']),
                        'download' => !empty($permissions['download']),
                        'export'   => !empty($permissions['export']),
                    ];
                }
            }
        
            // Update hierarchy table
            DB::table('hierarchies')
                ->where('id', $id)
                ->update([
                    'hierarchy_level' => $request->hierarchy_level,
                    'modules' => json_encode($modules),
                    'updated_at' => now(),
                ]);
        
            // Fetch all employees linked to this hierarchy
            $employeeAccessList = DB::table('employee_module_access')
                ->where('hierarchy_id', $id)
                ->get();
        
            // Delete old module access entries for all employees assigned to this hierarchy
            DB::table('employee_module_access')
                ->where('hierarchy_id', $id)
                ->delete();
        
            // Reinsert module access for each employee and module with new permissions
            foreach ($employeeAccessList as $access) {
                foreach ($modules as $moduleName => $permission) {
                    DB::table('employee_module_access')->insert([
                        'employee_id'   => $access->employee_id,
                        'hierarchy_id'  => $id,
                        'module_name'   => $moduleName,
                        'can_view'      => $permission['view'] ? 1 : 0,
                        'can_create'    => $permission['create'] ? 1 : 0,
                        'can_edit'      => $permission['edit'] ? 1 : 0,
                        'can_delete'    => $permission['delete'] ? 1 : 0,
                        'can_approve'   => $permission['approve'] ? 1 : 0,
                        'can_download'  => $permission['download'] ? 1 : 0,
                        'can_export'    => $permission['export'] ? 1 : 0,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        
            return redirect()->route('hierarchy.index')->with('success', 'Hierarchy and employee module access updated successfully!');
        
        } catch (\Exception $e) {
            Log::error('Error updating hierarchy ID ' . $id . ': ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Error updating hierarchy. Please try again. Error: ' . $e->getMessage());
        }
    }
    
        
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $hierarchy = DB::table('hierarchies')->where('id', $id)->first();
            
            if (!$hierarchy) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Hierarchy record not found!'
                ], 404);
            }

            $deleted = DB::table('hierarchies')->where('id', $id)->delete();
            
            if ($deleted) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Hierarchy record deleted successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to delete hierarchy record!'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting hierarchy: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting hierarchy. Please try again.'
            ], 500);
        }
    }

    /**
     * Get hierarchy modules for modal display
     */
    public function getHierarchyModules($id)
    {
        try {
            $hierarchy = DB::table('hierarchies')->where('id', $id)->first();
            
            if (!$hierarchy) {
                return response()->json(['error' => 'Hierarchy record not found'], 404);
            }
            
            // Decode JSON modules safely
            $modules = [];
            try {
                $modules = json_decode($hierarchy->modules ?? '[]', true);
                if (!is_array($modules)) {
                    $modules = [];
                }
            } catch (\Exception $e) {
                Log::error('Error decoding modules JSON for hierarchy ID ' . $id . ': ' . $e->getMessage());
                $modules = [];
            }
            
            return response()->json([
                'modules' => $modules,
                'hierarchy_level' => $hierarchy->hierarchy_level
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching hierarchy modules for ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Error loading modules'], 500);
        }
    }

    /**
     * Get hierarchy details
     */
    public function getHierarchyDetails($id)
    {
        try {
            $hierarchy = DB::table('hierarchies')->where('id', $id)->first();
            
            if (!$hierarchy) {
                return response()->json(['error' => 'Hierarchy record not found'], 404);
            }
            
            // Decode JSON modules safely
            try {
                $hierarchy->modules = json_decode($hierarchy->modules ?? '[]', true);
                if (!is_array($hierarchy->modules)) {
                    $hierarchy->modules = [];
                }
            } catch (\Exception $e) {
                Log::error('Error decoding modules JSON for hierarchy ID ' . $id . ': ' . $e->getMessage());
                $hierarchy->modules = [];
            }
            
            return response()->json([
                'hierarchy' => $hierarchy
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching hierarchy details for ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Error loading hierarchy details'], 500);
        }
    }
}
