<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdminaccessController extends Controller
{public function index(Request $request)
    {
        $query = DB::table('admin_access')
            ->leftJoin('hierarchies', 'admin_access.hierarchy_id', '=', 'hierarchies.id')
            ->leftJoin('branches', 'admin_access.branch_id', '=', 'branches.id')
            ->select(
                'admin_access.*',
                'hierarchies.hierarchy_level',
                'branches.name as branch_name',
                'branches.address as branch_address'
            );
    
        /*
        |--------------------------------------------------------------------------
        | Case-Insensitive Filters
        |--------------------------------------------------------------------------
        */
    
        // 🔎 Search by Admin Name (case-insensitive)
        if ($request->filled('admin_name')) {
            $query->whereRaw(
                'LOWER(admin_access.name) LIKE ?',
                ['%' . strtolower($request->admin_name) . '%']
            );
        }
    
        // 🔎 Search by Email (case-insensitive)
        if ($request->filled('email')) {
            $query->whereRaw(
                'LOWER(admin_access.email) LIKE ?',
                ['%' . strtolower($request->email) . '%']
            );
        }
    
        // 🔎 Filter by Branch
        if ($request->filled('branch_id')) {
            $query->where('admin_access.branch_id', $request->branch_id);
        }
    
        // 🔎 Filter by Hierarchy
        if ($request->filled('hierarchy_id')) {
            $query->where('admin_access.hierarchy_id', $request->hierarchy_id);
        }
    
        /*
        |--------------------------------------------------------------------------
        | Pagination (Recommended)
        |--------------------------------------------------------------------------
        */
        $admins = $query->latest('admin_access.id')
                        ->paginate(10)
                        ->appends($request->query());
    
    
        /*
        |--------------------------------------------------------------------------
        | Efficient Module Count (NO N+1)
        |--------------------------------------------------------------------------
        */
        $adminIds = $admins->pluck('id');
    
        $moduleCounts = DB::table('admin_module_access')
            ->select('admin_id', DB::raw('COUNT(*) as total'))
            ->whereIn('admin_id', $adminIds)
            ->groupBy('admin_id')
            ->pluck('total', 'admin_id');
    
        foreach ($admins as $admin) {
            $admin->module_count = $moduleCounts[$admin->id] ?? 0;
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | Filter Dropdown Data
        |--------------------------------------------------------------------------
        */
        $branches = DB::table('branches')
            ->where('status', 1)
            ->select('id', 'name')
            ->get();
    
        $hierarchies = DB::table('hierarchies')
            ->select('id', 'hierarchy_level')
            ->get();
    
        return view('hrms.master.adminaccess.index', compact(
            'admins',
            'branches',
            'hierarchies'
        ));
    }
    
    
    public function create()
    {
        // Fetch hierarchies from the database
        $hierarchies = DB::table('hierarchies')->orderBy('hierarchy_level')->get();
        
        // Show ALL active branches - no filtering
        $branches = DB::table('branches')
            ->where('status', 1)
            ->select('id', 'name', 'address')
            ->get();
        
        // Pass data to the view
        return view('hrms.master.adminaccess.add', compact('hierarchies', 'branches'));
    }

    public function store(Request $request)
    {
        // Validate the request - no branch restrictions
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_access',
            'password' => 'required|string|min:6',
            'hierarchy_id' => 'nullable|integer|exists:hierarchies,id',
            'branch_id' => 'nullable|integer|exists:branches,id', // Allow any valid branch
        ];

        $request->validate($validationRules);

        DB::beginTransaction();

        try {
            $hashedPassword = Hash::make($request->password);

            // Get hierarchy modules
            $hierarchy = DB::table('hierarchies')->where('id', $request->hierarchy_id)->first();
            $hierarchyModules = $hierarchy && $hierarchy->modules ?
                                json_decode($hierarchy->modules, true) : [];

            // Process module access
            $manuallySelectedModules = $request->modules ?? [];
            $allModules = array_unique(array_merge($hierarchyModules, $manuallySelectedModules));

          $hierarchyId = is_array($request->hierarchy_id) ? $request->hierarchy_id[0] : $request->hierarchy_id;
$branchId = is_array($request->branch_id) ? $request->branch_id[0] : $request->branch_id;

$adminId = DB::table('admin_access')->insertGetId([
    'name' => $request->name,
    'email' => $request->email,
    'password' => $hashedPassword,
    'hierarchy_id' => $hierarchyId,
    'branch_id' => $branchId,
    'status' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);
            // Insert module access
            if (!empty($allModules)) {
                $moduleData = array_map(function($module) use ($adminId, $hierarchyModules) {
                    return [
                        'admin_id' => $adminId,
                        'module_name' => trim($module),
                        'source' => in_array($module, $hierarchyModules) ? 'hierarchy' : 'manual',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $allModules);

                DB::table('admin_module_access')->insert($moduleData);
            }

            // Send welcome email
            $this->sendAdminCredentials($request->email, $request->name, $request->password);

            DB::commit();

            return redirect()->route('adminaccess.index')
                ->with('success', "Admin created successfully. " .
                       count($allModules) . " modules assigned.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Admin creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Admin creation failed: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $admin = DB::table('admin_access')->find($id);

        if (!$admin) {
            return redirect()->route('adminaccess.index')->withErrors(['message' => 'Admin not found.']);
        }

        $hierarchies = DB::table('hierarchies')->orderBy('hierarchy_level')->get();
        
        // Show ALL active branches - no filtering
        $branches = DB::table('branches')
            ->where('status', 1)
            ->select('id', 'name', 'address')
            ->get();

        // Get the modules the admin has access to
        $adminModules = DB::table('admin_module_access')
            ->where('admin_id', $id)
            ->pluck('module_name')
            ->toArray();

        return view('hrms.master.adminaccess.edit', compact(
            'admin',
            'hierarchies',
            'branches',
            'adminModules'
        ));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Find the admin
            $admin = DB::table('admin_access')->find($id);
            if (!$admin) {
                return redirect()->route('adminaccess.index')->withErrors(['message' => 'Admin not found.']);
            }

            // Validate the request - no branch restrictions
            $validationRules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admin_access,email,' . $id,
                'password' => 'nullable|string|min:6',
                'hierarchy_id' => 'nullable|integer|exists:hierarchies,id',
                'branch_id' => 'nullable|integer|exists:branches,id', // Allow any valid branch
            ];

            $request->validate($validationRules);

            // Get hierarchy modules
            $hierarchyModules = [];
            if ($request->hierarchy_id) {
                $hierarchy = DB::table('hierarchies')->where('id', $request->hierarchy_id)->first();
                if ($hierarchy && $hierarchy->modules) {
                    try {
                        $hierarchyModules = json_decode($hierarchy->modules, true);
                        if (!is_array($hierarchyModules)) {
                            $hierarchyModules = [];
                        }
                    } catch (\Exception $e) {
                        Log::error('Error decoding hierarchy modules: ' . $e->getMessage());
                        $hierarchyModules = [];
                    }
                }
            }

            // Get manually selected modules from form
            $manuallySelectedModules = $request->modules ?? [];

            // Merge hierarchy modules with manually selected modules
            $allModules = array_unique(array_merge($hierarchyModules, $manuallySelectedModules));

            // Prepare update data
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'hierarchy_id' => is_array($request->hierarchy_id) ? $request->hierarchy_id[0] : $request->hierarchy_id,
                'branch_id' => is_array($request->branch_id) ? $request->branch_id[0] : $request->branch_id,
                'updated_at' => now()
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Update admin data in the database
            DB::table('admin_access')
                ->where('id', $id)
                ->update($updateData);

            // Update admin modules - Remove all existing modules first
            DB::table('admin_module_access')
                ->where('admin_id', $id)
                ->delete();

            // Insert ALL modules (hierarchy + manual selections) into admin_module_access table
            if (!empty($allModules)) {
                $moduleInsertData = [];
                foreach ($allModules as $module) {
                    $moduleInsertData[] = [
                        'admin_id' => $id,
                        'module_name' => trim($module),
                        'source' => in_array($module, $hierarchyModules) ? 'hierarchy' : 'manual',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                DB::table('admin_module_access')->insert($moduleInsertData);
            }

            DB::commit();

            return redirect()->route('adminaccess.index')
                ->with('success', 'Admin updated successfully! ' .
                      count($allModules) . ' modules assigned (' .
                       count($hierarchyModules) . ' from hierarchy, ' .
                       count($manuallySelectedModules) . ' manually selected).');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating admin: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating admin: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            // Find the admin
            $admin = DB::table('admin_access')->find($id);
            if (!$admin) {
                return redirect()->route('adminaccess.index')->withErrors(['message' => 'Admin not found.']);
            }

            // First delete all related module access records
            DB::table('admin_module_access')->where('admin_id', $id)->delete();
            
            // Then permanently delete the admin record
            $deleted = DB::table('admin_access')->where('id', $id)->delete();
            
            if ($deleted) {
                DB::commit();
                return redirect()->route('adminaccess.index')->with('success', 'Admin permanently deleted successfully.');
            } else {
                DB::rollback();
                return redirect()->back()->withErrors(['message' => 'Admin not found.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error permanently deleting admin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Failed to delete admin: ' . $e->getMessage()]);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $admin = DB::table('admin_access')->find($id);
            
            if (!$admin) {
                return response()->json(['success' => false, 'message' => 'Admin not found.'], 404);
            }

            $newStatus = $admin->status == 1 ? 0 : 1;
            
            DB::table('admin_access')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);

            $statusText = $newStatus == 1 ? 'Active' : 'Inactive';
            
            return response()->json([
                'success' => true,
                'message' => "Admin status changed to {$statusText} successfully!",
                'new_status' => $newStatus,
                'status_text' => $statusText
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error changing admin status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to change status.'], 500);
        }
    }

    public function show($id)
    {
        // Fetch the admin with all related data including hierarchy and branch
        $admin = DB::table('admin_access')
            ->leftJoin('hierarchies', 'admin_access.hierarchy_id', '=', 'hierarchies.id')
            ->leftJoin('branches', 'admin_access.branch_id', '=', 'branches.id')
            ->select(
                'admin_access.*',
                'hierarchies.hierarchy_level',
                'branches.name as branch_name',
                'branches.address as branch_address'
            )
            ->where('admin_access.id', $id)
            ->first();

        if (!$admin) {
            return redirect()->route('adminaccess.index')->withErrors(['message' => 'Admin not found.']);
        }

        // Fetch admin modules with source information
        $adminModules = DB::table('admin_module_access')
            ->where('admin_id', $id)
            ->select('module_name', 'source')
            ->get();

        return view('hrms.master.adminaccess.show', compact('admin', 'adminModules'));
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $emailExists = DB::table('admin_access')->where('email', $request->email)->exists();

        return response()->json(['exists' => $emailExists]);
    }

    public function getHierarchyModules($hierarchyId)
    {
        try {
            $hierarchy = DB::table('hierarchies')->where('id', $hierarchyId)->first();

            if (!$hierarchy) {
                return response()->json(['error' => 'Hierarchy not found'], 404);
            }

            $modules = [];
            if ($hierarchy->modules) {
                try {
                    $modules = json_decode($hierarchy->modules, true);
                    if (!is_array($modules)) {
                        $modules = [];
                    }
                } catch (\Exception $e) {
                    Log::error('Error decoding hierarchy modules: ' . $e->getMessage());
                    $modules = [];
                }
            }

            return response()->json([
                'success' => true,
                'modules' => $modules,
                'hierarchy_level' => $hierarchy->hierarchy_level
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching hierarchy modules: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading modules'], 500);
        }
    }

    protected function sendAdminCredentials($email, $name, $password)
    {
        try {
            $details = [
                'title' => 'Admin Account Created!',
                'body' => "Dear $name,\n\nYour admin account has been created successfully. Below are your login credentials:\n\nEmail: $email\nPassword: $password\n\nPlease log in and change your password at your earliest convenience.\n\nFor more information, visit our website: https://tecla.in/mms/public/\n\nBest Regards,\nAdmin Team"
            ];

            Mail::raw($details['body'], function ($message) use ($email, $details) {
                $message->to($email)
                        ->subject($details['title']);
            });

            Log::info('Admin credentials email sent successfully to: ' . $email);
        } catch (\Exception $e) {
            Log::error('Failed to send admin credentials email: ' . $e->getMessage());
        }
    }
}