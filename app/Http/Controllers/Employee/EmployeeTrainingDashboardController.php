<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmployeeTrainingDashboardController extends Controller
{
    public function index()
    {
        // Get employee ID from session
        $employeeId = Session::get('user_id');
        
        if (!$employeeId) {
            return redirect()->route('login')->with('error', 'Please login to access training dashboard.');
        }

        // Get employee details
        $employee = DB::table('allemployees')
            ->where('id', $employeeId)
            ->where('deleted_at', 0)
            ->first();

        if (!$employee) {
            return redirect()->route('login')->with('error', 'Employee not found.');
        }

        // Get employee training details
        $trainingDetails = DB::table('employee_training_details')
            ->where('employee_id', $employeeId)
            ->first();

        // Get trainer details if assigned
        $trainer = null;
        if ($employee->trainer_id) {
            $trainer = DB::table('trainers')
                ->where('id', $employee->trainer_id)
                ->first();
        }

        // Parse training resources
        $trainingResources = [];
        $completedCount = 0;
        $totalCount = 0;

        if ($trainingDetails && $trainingDetails->training_resources) {
            $resources = json_decode($trainingDetails->training_resources, true);
            if (is_array($resources)) {
                $trainingResources = $resources;
                $totalCount = count($resources);
                $completedCount = count(array_filter($resources, function($resource) {
                    return isset($resource['is_completed']) && $resource['is_completed'] === true;
                }));
            }
        }

        // Calculate progress percentage
        $progressPercentage = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;

        // Group resources by type
        $groupedResources = [
            'module' => [],
            'video' => [],
            'link' => [],
            'document' => []
        ];

        foreach ($trainingResources as $resource) {
            $type = $resource['resource_type'] ?? 'module';
            $groupedResources[$type][] = $resource;
        }

        return view('hrms.performance.Training.training-dashboard.index', compact(
            'employee',
            'trainingDetails',
            'trainer',
            'trainingResources',
            'groupedResources',
            'completedCount',
            'totalCount',
            'progressPercentage'
        ));
    }

    public function markResourceCompleted(Request $request, $resourceId)
    {
        $employeeId = Session::get('user_id');
        
        if (!$employeeId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $training = DB::table('employee_training_details')
                ->where('employee_id', $employeeId)
                ->first();

            if ($training && $training->training_resources) {
                $resources = json_decode($training->training_resources, true);
                
                // Find and update the specific resource
                $resourceFound = false;
                foreach ($resources as &$resource) {
                    if ($resource['id'] === $resourceId) {
                        $resource['is_completed'] = true;
                        $resource['completed_at'] = now()->toDateTimeString();
                        $resourceFound = true;
                        break;
                    }
                }

                if (!$resourceFound) {
                    return response()->json(['success' => false, 'message' => 'Resource not found'], 404);
                }

                // Update the training record
                DB::table('employee_training_details')
                    ->where('employee_id', $employeeId)
                    ->update([
                        'training_resources' => json_encode($resources),
                        'updated_at' => now(),
                    ]);

                // Calculate new progress
                $completedCount = count(array_filter($resources, function($resource) {
                    return isset($resource['is_completed']) && $resource['is_completed'] === true;
                }));
                $totalCount = count($resources);
                $progressPercentage = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;

                return response()->json([
                    'success' => true, 
                    'message' => 'Resource marked as completed successfully!',
                    'progress' => round($progressPercentage, 1),
                    'completed' => $completedCount,
                    'total' => $totalCount
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Training record not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error marking resource as completed', [
                'message' => $e->getMessage(),
                'employee_id' => $employeeId,
                'resource_id' => $resourceId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update resource status'], 500);
        }
    }

    public function downloadResource($resourceId)
    {
        $employeeId = Session::get('user_id');
        
        if (!$employeeId) {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        try {
            $training = DB::table('employee_training_details')
                ->where('employee_id', $employeeId)
                ->first();

            if ($training && $training->training_resources) {
                $resources = json_decode($training->training_resources, true);
                
                foreach ($resources as $resource) {
                    if ($resource['id'] === $resourceId && isset($resource['file_path'])) {
                        $filePath = $resource['file_path'];
                        
                        if (Storage::disk('public')->exists($filePath)) {
                            return Storage::disk('public')->download($filePath, $resource['title'] ?? 'training_resource');
                        }
                    }
                }
            }

            return redirect()->back()->with('error', 'File not found.');
        } catch (\Exception $e) {
            Log::error('Error downloading resource', [
                'message' => $e->getMessage(),
                'employee_id' => $employeeId,
                'resource_id' => $resourceId
            ]);
            return redirect()->back()->with('error', 'Failed to download file.');
        }
    }
}