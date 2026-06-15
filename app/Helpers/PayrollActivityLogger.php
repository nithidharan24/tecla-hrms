<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class PayrollActivityLogger
{
    /**
     * Log an activity in the payroll_activity_log table
     *
     * @param string $module
     * @param string $action
     * @param string $description
     * @param int|null $employeeId
     * @param array|null $oldData
     * @param array|null $newData
     * @param Request|null $request
     * @return void
     */
    public static function logActivity(
        string $module,
        string $action,
        string $description,
        ?int $employeeId = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?Request $request = null
    ) {
        $userType = Session::get('role', 'system');
        $userId = null;
        $userName = null;
        $userEmail = null;
        
        // Get user information based on role
        if ($userType === 'admin') {
            $userId = Session::get('admin_id');
            $userName = Session::get('admin_name');
            $userEmail = Session::get('email');
            
            // Fetch admin details from database if session data is incomplete
            if (!$userName || !$userEmail) {
                $admin = DB::table('admin_access')->where('id', $userId)->first();
                if ($admin) {
                    $userName = $admin->name;
                    $userEmail = $admin->email;
                }
            }
        } elseif ($userType === 'employee') {
            $userId = Session::get('user_id');
            $employee = DB::table('allemployees')->where('id', $userId)->first();
            if ($employee) {
                $userName = $employee->firstname . ' ' . $employee->lastname;
                $userEmail = $employee->email;
            }
        }
        
        // Get employee details for the affected employee
        $employeeDetails = null;
        if ($employeeId) {
            $employee = DB::table('allemployees')->where('id', $employeeId)->first();
            if ($employee) {
                $employeeDetails = [
                    'employee_id' => $employee->employeeid,
                    'name' => $employee->firstname . ' ' . $employee->lastname,
                    'email' => $employee->email
                ];
            }
        }
        
        // Get IP address
        $ipAddress = $request ? $request->ip() : request()->ip();
        
        // Prepare data for insertion
        $logData = [
            'user_type' => $userType,
            'user_id' => $userId,
            'user_name' => $userName,
            'email' => $userEmail,
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'employee_id' => $employeeId,
            'employee_details' => $employeeDetails ? json_encode($employeeDetails) : null,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $ipAddress,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        // Insert into activity log table
        DB::table('payroll_activity_log')->insert($logData);
    }
    
    /**
     * Log creation activity
     */
    public static function logCreate(string $module, string $itemName, ?int $employeeId = null, ?array $data = null)
    {
        self::logActivity(
            $module,
            'create',
            "Created {$itemName} in {$module}",
            $employeeId,
            null,
            $data
        );
    }
    
    /**
     * Log update activity
     */
    public static function logUpdate(string $module, string $itemName, ?int $employeeId = null, ?array $oldData = null, ?array $newData = null)
    {
        self::logActivity(
            $module,
            'update',
            "Updated {$itemName} in {$module}",
            $employeeId,
            $oldData,
            $newData
        );
    }
    
    /**
     * Log delete activity
     */
    public static function logDelete(string $module, string $itemName, ?int $employeeId = null, ?array $oldData = null)
    {
        self::logActivity(
            $module,
            'delete',
            "Deleted {$itemName} from {$module}",
            $employeeId,
            $oldData,
            null
        );
    }
    
    /**
     * Log generate activity
     */
    public static function logGenerate(string $module, string $description, ?int $employeeId = null, ?array $data = null)
    {
        self::logActivity(
            $module,
            'generate',
            $description,
            $employeeId,
            null,
            $data
        );
    }
    
    /**
     * Log email sending activity
     */
    public static function logEmail(string $module, string $description, ?int $employeeId = null)
    {
        self::logActivity(
            $module,
            'send_email',
            $description,
            $employeeId
        );
    }
}