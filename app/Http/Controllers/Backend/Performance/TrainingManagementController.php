<?php

namespace App\Http\Controllers\Backend\Performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class TrainingManagementController extends Controller
{
    // Function to display employees who need training
    public function index()
    {
        $employeesNeedingTraining = DB::table('allemployees')
            ->leftJoin('trainers', 'allemployees.trainer_id', '=', 'trainers.id')
            ->leftJoin('employee_training_details', 'allemployees.id', '=', 'employee_training_details.employee_id')
            ->where('allemployees.training_needed', 'Yes')
            ->where('allemployees.deleted_at', 0)
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.profile_image',
                DB::raw('CONCAT(trainers.first_name, " ", trainers.last_name) as trainer_name'),
                'employee_training_details.start_date',
                'employee_training_details.end_date',
                'employee_training_details.description as training_description',
                'employee_training_details.status as training_status',
                'employee_training_details.training_type',
                'employee_training_details.duration_hours',
                'employee_training_details.training_resources',
                // New fields
                'employee_training_details.training_name',
                'employee_training_details.training_category',
                'employee_training_details.trainer_type',
                'employee_training_details.training_mode',
                'employee_training_details.training_location',
                'employee_training_details.department_eligibility',
                'employee_training_details.training_cost',
                'employee_training_details.certification_required',
                'employee_training_details.max_participants'
            )
            ->get();
        
        // Set default status for employees without training details
        foreach ($employeesNeedingTraining as $employee) {
            if (!$employee->training_status) {
                $employee->training_status = 'planned'; // Changed from 'pending' to 'planned'
            }
            
            // Decode training resources JSON - Fixed JSON handling
            $employee->resources = [];
            if ($employee->training_resources && $employee->training_resources !== 'null') {
                $decodedResources = json_decode($employee->training_resources, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedResources)) {
                    $employee->resources = $decodedResources;
                }
            }
            $employee->resources_count = count($employee->resources);
        }

        return view('hrms.performance.Training.Training.index', compact('employeesNeedingTraining'));
    }

    // Function to show edit form for employee training details
    public function editEmployee($id)
    {
        $employee = DB::table('allemployees')
            ->leftJoin('trainers', 'allemployees.trainer_id', '=', 'trainers.id')
            ->leftJoin('employee_training_details', 'allemployees.id', '=', 'employee_training_details.employee_id')
            ->where('allemployees.id', $id)
            ->where('allemployees.training_needed', 'Yes')
            ->select(
                'allemployees.id',
                'allemployees.firstname',
                'allemployees.lastname',
                'allemployees.employeeid',
                'allemployees.profile_image',
                DB::raw('CONCAT(trainers.first_name, " ", trainers.last_name) as trainer_name'),
                'employee_training_details.start_date',
                'employee_training_details.end_date',
                'employee_training_details.description as training_description',
                'employee_training_details.status as training_status',
                'employee_training_details.training_type',
                'employee_training_details.duration_hours',
                'employee_training_details.training_resources',
                // New fields
                'employee_training_details.training_name',
                'employee_training_details.training_category',
                'employee_training_details.trainer_type',
                'employee_training_details.training_mode',
                'employee_training_details.training_location',
                'employee_training_details.department_eligibility',
                'employee_training_details.training_cost',
                'employee_training_details.certification_required',
                'employee_training_details.max_participants'
            )
            ->first();

        if (!$employee) {
            return redirect()->route('trainings.index')->with('error', 'Employee not found or does not need training.');
        }

        // Decode training resources JSON - Fixed JSON handling
        $trainingResources = [];
        if ($employee->training_resources && $employee->training_resources !== 'null') {
            $decodedResources = json_decode($employee->training_resources, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedResources)) {
                $trainingResources = $decodedResources;
            }
        }

        return view('hrms.performance.Training.Training.edit_employee', compact('employee', 'trainingResources'));
    }

    // Function to update employee training details - REMOVED VALIDATIONS
    public function updateEmployee(Request $request, $id)
    {
        // Debug: Log all request data
        Log::info('Training Update Request Data:', $request->all());

        try {
            DB::beginTransaction();

            // Prepare training resources array
            $trainingResources = [];
            
            // Debug: Check if resource data exists
            Log::info('Resource Titles:', ['titles' => $request->resource_titles ?? []]);
            Log::info('Resource Types:', ['types' => $request->resource_types ?? []]);
            Log::info('Is Mandatory:', ['mandatory' => $request->is_mandatory ?? []]);

            if ($request->has('resource_titles') && is_array($request->resource_titles)) {
                $resourceTitles = $request->resource_titles;
                $resourceTypes = $request->resource_types ?? [];
                $resourceDescriptions = $request->resource_descriptions ?? [];
                $resourceUrls = $request->resource_urls ?? [];
                $isMandatory = $request->is_mandatory ?? [];
                $orderSequences = $request->order_sequence ?? [];

                foreach ($resourceTitles as $index => $title) {
                    if (!empty(trim($title))) {
                        $resourceData = [
                            'id' => uniqid('resource_', true),
                            'resource_type' => $resourceTypes[$index] ?? 'module',
                            'title' => trim($title),
                            'description' => isset($resourceDescriptions[$index]) && !empty(trim($resourceDescriptions[$index])) ? trim($resourceDescriptions[$index]) : null,
                            'external_url' => isset($resourceUrls[$index]) && !empty(trim($resourceUrls[$index])) ? trim($resourceUrls[$index]) : null,
                            'is_mandatory' => isset($isMandatory[$index]) && $isMandatory[$index] == '1' ? true : false,
                            'order_sequence' => isset($orderSequences[$index]) ? (int)$orderSequences[$index] : $index,
                            'is_completed' => false,
                            'completed_at' => null,
                            'file_path' => null,
                            'created_at' => now()->toDateTimeString(),
                        ];

                        // Handle file upload
                        if ($request->hasFile("resource_files.$index")) {
                            $file = $request->file("resource_files.$index");
                            if ($file->isValid()) {
                                $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                                $filePath = $file->storeAs('training_resources', $fileName, 'public');
                                $resourceData['file_path'] = $filePath;
                                Log::info('File uploaded successfully', [
                                    'index' => $index,
                                    'file_path' => $filePath,
                                    'original_name' => $file->getClientOriginalName()
                                ]);
                            }
                        }

                        $trainingResources[] = $resourceData;
                        Log::info('Added resource', [
                            'index' => $index,
                            'resource_data' => $resourceData
                        ]);
                    }
                }
            }

            // Sort resources by order sequence
            if (!empty($trainingResources)) {
                usort($trainingResources, function($a, $b) {
                    return $a['order_sequence'] <=> $b['order_sequence'];
                });
            }

            // Debug: Log prepared resources
            Log::info('Final Training Resources Array:', ['resources' => $trainingResources]);

            // Convert department eligibility array to string
            $departmentEligibility = $request->has('department_eligibility') 
                ? implode(',', $request->department_eligibility) 
                : null;

            // Check if training details already exist
            $existingTraining = DB::table('employee_training_details')
                ->where('employee_id', $id)
                ->first();

            // Prepare training data with new fields
            $trainingData = [
                'employee_id' => $id,
                // New fields
                'training_name' => $request->training_name,
                'training_category' => $request->training_category,
                'trainer_name' => $request->trainer_name,
                'trainer_type' => $request->trainer_type,
                'training_mode' => $request->training_mode,
                'training_location' => $request->training_location,
                'department_eligibility' => $departmentEligibility,
                'training_cost' => $request->training_cost,
                'certification_required' => $request->has('certification_required') ? 1 : 0,
                'max_participants' => $request->max_participants,
                // Existing fields
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'training_type' => $request->training_type,
                'description' => $request->description,
                'duration_hours' => $request->duration_hours,
                'status' => $request->training_status,
                'updated_at' => now(),
            ];

            // Handle training resources JSON
            if (!empty($trainingResources)) {
                $jsonResources = json_encode($trainingResources, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $trainingData['training_resources'] = $jsonResources;
                Log::info('JSON Resources prepared', [
                    'json_string' => $jsonResources,
                    'json_length' => strlen($jsonResources)
                ]);
            } else {
                $trainingData['training_resources'] = null;
                Log::info('No training resources to save');
            }

            // Debug: Log training data
            Log::info('Training Data to Save:', ['training_data' => $trainingData]);

            if ($existingTraining) {
                // Delete old files if they exist
                if ($existingTraining->training_resources && $existingTraining->training_resources !== 'null') {
                    $oldResources = json_decode($existingTraining->training_resources, true);
                    if (is_array($oldResources)) {
                        foreach ($oldResources as $oldResource) {
                            if (isset($oldResource['file_path']) && $oldResource['file_path']) {
                                Storage::disk('public')->delete($oldResource['file_path']);
                                Log::info('Deleted old file', ['file_path' => $oldResource['file_path']]);
                            }
                        }
                    }
                }

                // Update existing record
                $updated = DB::table('employee_training_details')
                    ->where('employee_id', $id)
                    ->update($trainingData);
                
                Log::info('Update operation completed', [
                    'updated' => $updated,
                    'employee_id' => $id
                ]);
            } else {
                // Create new record
                $trainingData['created_at'] = now();
                $inserted = DB::table('employee_training_details')->insert($trainingData);
                
                Log::info('Insert operation completed', ['inserted' => $inserted]);
            }

            DB::commit();
            
            // Verify the data was saved
            $verifyData = DB::table('employee_training_details')
                ->where('employee_id', $id)
                ->first();
            
            Log::info('Verification after save', [
                'employee_id' => $verifyData->employee_id ?? 'NOT FOUND',
                'has_training_resources' => !empty($verifyData->training_resources ?? null),
                'training_resources_length' => strlen($verifyData->training_resources ?? ''),
                'training_resources_preview' => substr($verifyData->training_resources ?? '', 0, 100) . '...'
            ]);
            
            return redirect()->route('trainings.index')->with('success', 'Training details and resources updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating employee training details', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'employee_id' => $id
            ]);
            
            return redirect()->back()->withInput()->with('error', 'Failed to update training details: ' . $e->getMessage());
        }
    }

    // Function to update employee training status (existing method - updated for new status values)
 public function updateEmployeeStatus(Request $request, $id)
{
    try {
        // Validate
        $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed'
        ]);

        // Check employee exists
        $employee = DB::table('allemployees')->where('id', $id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        // 🔥 IMPORTANT: update correct table
        DB::table('employee_training_details')
            ->updateOrInsert(
                ['employee_id' => $id],
                [
                    'status' => $request->status,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);

    } catch (\Exception $e) {

        Log::error('Status update error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
    // Function to mark resource as completed (existing method)
    public function markResourceCompleted(Request $request, $employeeId, $resourceId)
    {
        try {
            $training = DB::table('employee_training_details')
                ->where('employee_id', $employeeId)
                ->first();

            if ($training && $training->training_resources) {
                $resources = json_decode($training->training_resources, true);
                
                // Find and update the specific resource
                foreach ($resources as &$resource) {
                    if ($resource['id'] === $resourceId) {
                        $resource['is_completed'] = true;
                        $resource['completed_at'] = now()->toDateTimeString();
                        break;
                    }
                }

                // Update the training record
                DB::table('employee_training_details')
                    ->where('employee_id', $employeeId)
                    ->update([
                        'training_resources' => json_encode($resources),
                        'updated_at' => now(),
                    ]);

                return response()->json(['success' => true, 'message' => 'Resource marked as completed']);
            }

            return response()->json(['success' => false, 'message' => 'Training record not found']);

        } catch (\Exception $e) {
            Log::error('Error marking resource as completed', [
                'message' => $e->getMessage(),
                'employee_id' => $employeeId,
                'resource_id' => $resourceId
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update resource status']);
        }
    }

    // Function to delete training resource (existing method)
    public function deleteResource($employeeId, $resourceId)
    {
        try {
            $training = DB::table('employee_training_details')
                ->where('employee_id', $employeeId)
                ->first();

            if ($training && $training->training_resources) {
                $resources = json_decode($training->training_resources, true);
                
                // Find and remove the specific resource
                $updatedResources = [];
                foreach ($resources as $resource) {
                    if ($resource['id'] === $resourceId) {
                        // Delete file if exists
                        if (isset($resource['file_path']) && $resource['file_path']) {
                            Storage::disk('public')->delete($resource['file_path']);
                        }
                    } else {
                        $updatedResources[] = $resource;
                    }
                }

                // Update the training record
                DB::table('employee_training_details')
                    ->where('employee_id', $employeeId)
                    ->update([
                        'training_resources' => json_encode($updatedResources),
                        'updated_at' => now(),
                    ]);

                return redirect()->back()->with('success', 'Training resource deleted successfully!');
            }

            return redirect()->back()->with('error', 'Training record not found.');

        } catch (\Exception $e) {
            Log::error('Error deleting training resource', [
                'message' => $e->getMessage(),
                'employee_id' => $employeeId,
                'resource_id' => $resourceId
            ]);
            return redirect()->back()->with('error', 'Failed to delete training resource.');
        }
    }

    // Function to get training progress (existing method)
    public function getTrainingProgress($employeeId)
    {
        try {
            $training = DB::table('employee_training_details')
                ->where('employee_id', $employeeId)
                ->first();

            if ($training && $training->training_resources) {
                $resources = json_decode($training->training_resources, true);
                $totalResources = count($resources);
                $completedResources = count(array_filter($resources, function($resource) {
                    return $resource['is_completed'] === true;
                }));

                $progress = $totalResources > 0 ? ($completedResources / $totalResources) * 100 : 0;

                return response()->json([
                    'success' => true,
                    'total_resources' => $totalResources,
                    'completed_resources' => $completedResources,
                    'progress_percentage' => round($progress, 2)
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No training data found']);

        } catch (\Exception $e) {
            Log::error('Error getting training progress', [
                'message' => $e->getMessage(),
                'employee_id' => $employeeId
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to get training progress']);
        }
    }

    // New function to get training statistics
    public function getTrainingStatistics()
    {
        try {
            $statistics = DB::table('employee_training_details')
                ->select(
                    DB::raw('COUNT(*) as total_trainings'),
                    DB::raw('COUNT(CASE WHEN status = "planned" THEN 1 END) as planned_trainings'),
                    DB::raw('COUNT(CASE WHEN status = "ongoing" THEN 1 END) as ongoing_trainings'),
                    DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_trainings'),
                    DB::raw('COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_trainings'),
                    DB::raw('SUM(training_cost) as total_cost'),
                    DB::raw('AVG(duration_hours) as avg_duration')
                )
                ->first();

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting training statistics', [
                'message' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to get training statistics']);
        }
    }

    // New function to export training data
    public function exportTrainingData()
    {
        try {
            $trainings = DB::table('employee_training_details')
                ->join('allemployees', 'employee_training_details.employee_id', '=', 'allemployees.id')
                ->select(
                    'allemployees.firstname',
                    'allemployees.lastname',
                    'allemployees.employeeid',
                    'employee_training_details.training_name',
                    'employee_training_details.training_category',
                    'employee_training_details.trainer_name',
                    'employee_training_details.trainer_type',
                    'employee_training_details.training_mode',
                    'employee_training_details.start_date',
                    'employee_training_details.end_date',
                    'employee_training_details.status',
                    'employee_training_details.training_cost',
                    'employee_training_details.duration_hours'
                )
                ->get();

            // Here you would typically generate CSV or Excel file
            // For now, return JSON response
            return response()->json([
                'success' => true,
                'data' => $trainings
            ]);

        } catch (\Exception $e) {
            Log::error('Error exporting training data', [
                'message' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to export training data']);
        }
    }


public function showFeedbackForm($employeeId, $trainingId)
    {
        try {
            $employee = DB::table('allemployees')
                ->where('id', $employeeId)
                ->first();

            $training = DB::table('employee_training_details')
                ->where('id', $trainingId)
                ->where('employee_id', $employeeId)
                ->first();

            if (!$employee || !$training) {
                return response()->json(['success' => false, 'message' => 'Employee or training not found'], 404);
            }

            // Check if feedback already exists
            $existingFeedback = DB::table('training_feedback')
                ->where('employee_id', $employeeId)
                ->where('training_id', $trainingId)
                ->first();

            return response()->json([
                'success' => true,
                'employee' => $employee,
                'training' => $training,
                'feedback' => $existingFeedback
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading feedback form', [
                'message' => $e->getMessage(),
                'employee_id' => $employeeId,
                'training_id' => $trainingId
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to load feedback form'], 500);
        }
    }

    public function submitFeedback(Request $request, $employeeId, $trainingId)
    {
        try {
            Log::info('Feedback submission started', [
                'employee_id' => $employeeId,
                'training_id' => $trainingId,
                'request_data' => $request->all()
            ]);

            DB::beginTransaction();

            // REMOVED ALL VALIDATIONS - accept any input
            $feedbackData = [
                'employee_id' => $employeeId,
                'training_id' => $trainingId,
                'feedback_rating' => $request->feedback_rating ?: 0,
                'feedback_comments' => $request->feedback_comments ?: '',
                'trainer_feedback' => $request->trainer_feedback ?: '',
                'assessment_score' => $request->assessment_score ?: 0,
                'certificate_status' => $request->certificate_status ?: 'Not Issued',
                'updated_at' => now(),
            ];

            Log::info('Feedback data prepared (NO VALIDATION)', ['feedback_data' => $feedbackData]);

            // Verify employee exists
            $employee = DB::table('allemployees')
                ->where('id', $employeeId)
                ->first();

            if (!$employee) {
                Log::error('Employee not found', ['employee_id' => $employeeId]);
                DB::rollBack();
                return redirect()->back()->with('error', 'Employee not found');
            }

            // Check if feedback already exists
            $existingFeedback = DB::table('training_feedback')
                ->where('employee_id', $employeeId)
                ->where('training_id', $trainingId)
                ->first();

            if ($existingFeedback) {
                // Update existing feedback
                $updated = DB::table('training_feedback')
                    ->where('employee_id', $employeeId)
                    ->where('training_id', $trainingId)
                    ->update($feedbackData);

                Log::info('Feedback updated successfully', [
                    'employee_id' => $employeeId,
                    'training_id' => $trainingId,
                    'updated_rows' => $updated
                ]);
                
                $message = 'Feedback updated successfully!';
            } else {
                // Create new feedback
                $feedbackData['created_at'] = now();
                $inserted = DB::table('training_feedback')->insert($feedbackData);

                Log::info('Feedback created successfully', [
                    'employee_id' => $employeeId,
                    'training_id' => $trainingId,
                    'inserted' => $inserted
                ]);
                
                $message = 'Feedback submitted successfully!';
            }

            DB::commit();

            return redirect()->route('trainings.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error submitting feedback', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'employee_id' => $employeeId,
                'training_id' => $trainingId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit feedback. Please try again.');
        }
    }
    
}