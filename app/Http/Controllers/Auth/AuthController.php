<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    // Show Login Page
    public function showLoginPage()
    {
        // Redirect already-authenticated users to their dashboard
        if (Session::has('role')) {
            $role = Session::get('role');
            if ($role === 'admin') return redirect()->route('admin.dashboard');
            if ($role === 'manager') return redirect()->route('manager.dashboard');
            if ($role === 'hr') return redirect()->route('hr.dashboard');
            return redirect()->route('employee.home');
        }
        return view('auth.login.index');
    }
    
    // Show Login Page
    public function dashboard()
    {
        return view('hrms.admin.dashboard.index');
    }
    
public function login(Request $request)
{
    // Validate the request inputs
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);
    
    // Check Admin Login
    $admin = DB::table('admin_access')->where('email', $request->email)->first();
    if ($admin && Hash::check($request->password, $admin->password)) {
        // Check if admin is active
        if ($admin->status != 1) {
            return back()->withErrors('Your account is inactive. Please contact administrator.');
        }
        
        // Get admin's branch information
        $adminBranch = null;
        if ($admin->branch_id) {
            $adminBranch = DB::table('branches')->where('id', $admin->branch_id)->first();
        }
        
        // Store admin session details including branch information
        Session::put('admin_id', $admin->id);
        Session::put('role', 'admin');
        Session::put('email', $admin->email);
        Session::put('admin_name', $admin->name);
        Session::put('hierarchy_id', $admin->hierarchy_id);
        Session::put('branch_id', $admin->branch_id);
        Session::put('branch_name', $adminBranch ? $adminBranch->name : null);
        Session::put('branch_address', $adminBranch ? $adminBranch->address : null);
        
        return redirect()->route('admin.dashboard');
    }
    
    // Check Employee Login
    $employee = DB::table('allemployees')->where('email', $request->email)->first();
    
    if ($employee && Hash::check($request->password, $employee->password)) {
        // Get hierarchy info
        $hierarchy = DB::table('hierarchies')->where('id', $employee->hierarchy_id)->first();
        
        // Determine the role based on hierarchy level
        $role = 'employee'; // default role
        $hierarchyLevel = $hierarchy ? strtolower(trim($hierarchy->hierarchy_level)) : '';
        
        // Check if the employee is HR
        if ($hierarchyLevel === 'hr' || $hierarchyLevel === 'hr manager') {
            $role = 'hr';
        } elseif ($hierarchyLevel === 'manager') {
            $role = 'manager';
        }

        // Store employee session details
        Session::put('role', $role);
        Session::put('user_id', $employee->id);
        Session::put('first_name', $employee->firstname);
        Session::put('last_name', $employee->lastname);
        Session::put('employee_id', $employee->employeeid);
        Session::put('email', $employee->email);
        Session::put('branch_id', $employee->branch_id);
        Session::put('hierarchy_id', $employee->hierarchy_id);
        Session::put('hierarchy_level', $hierarchy ? $hierarchy->hierarchy_level : null);
        Session::put('profile_image', $employee->profile_image ?? null);

        // Get dashboard route based on hierarchy
        $dashboardRoute = $this->getDashboardRouteByHierarchy($hierarchy ? $hierarchy->hierarchy_level : null);
        
        // For HR users, redirect to the same employee home route
        // The TeamLeaveController will handle the HR logic based on the role
        return redirect()->route($dashboardRoute);
    }
    
    // If no user found or password is wrong, return with error
    return back()->withErrors('Invalid email or password.')->withInput($request->except('password'));
}
/**
 * Get dashboard route name based on hierarchy level
 */
private function getDashboardRouteByHierarchy($hierarchyLevel)
{
    if (!$hierarchyLevel) {
        return 'employee.home'; // default fallback
    }

    $level = strtolower(trim($hierarchyLevel));

    switch ($level) {
        case 'hr':
        case 'hr manager':
            return 'hr.dashboard';

        case 'manager':
            return 'manager.dashboard';

        default:
            return 'employee.home'; // all other employees go to home page
    }
}
    
    // Handle Logout
    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // Admin Dashboard
    public function adminDashboard()
    {
        return view('hrms.admin.dashboard.index');
    }

    // Employee Dashboard
    public function employeeDashboard()
    {
        return view('hrms.admin.dashboard.index');
    }
    
     
    // Show Forgot Password Page
    public function showForgotPasswordPage()
    {
        return view('auth.forgot.index');
    }

    public function handleForgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Check admin_access, allemployees, and clients tables
        $admin = DB::table('admin_access')->where('email', $request->email)->first();
        $employee = DB::table('allemployees')->where('email', $request->email)->first();
        $client = DB::table('clients')->where('email', $request->email)->first();
        
        if (!$admin && !$employee && !$client) {
            return back()->withErrors('Email not found.');
        }
        
        // Determine the user and their role
        $user = $admin ?? $employee ?? $client;
        $role = $admin ? 'admin' : ($employee ? 'employee' : 'client');
        
        // Generate a new random password
        $newPassword = Str::random(10);
        
        // Update the password in the respective table
        if ($role === 'admin') {
            DB::table('admin_access')->where('email', $request->email)->update(['password' => Hash::make($newPassword)]);
        } elseif ($role === 'employee') {
            DB::table('allemployees')->where('email', $request->email)->update(['password' => Hash::make($newPassword)]);
        } else {
            DB::table('clients')->where('email', $request->email)->update(['password' => Hash::make($newPassword)]);
        }
        
        // Send the new password via email
        Mail::send([], [], function ($message) use ($user, $newPassword) {
            $message->to($user->email)
                ->subject('Your New Password')
                ->html("Hello, your new password is: <strong>{$newPassword}</strong>. Please log in and change it as soon as possible.");
        });
        
        return back()->with('success', 'A new password has been sent to your email.');
    }
    
    // Show Reset Password Page
    public function showResetPasswordPage()
    {
        return view('auth.reset.index');
    }
    public function handleResetPassword(Request $request)
    {
        
        // Check the role from the session
        $role = Session::get('role');
        $userId = Session::get('user_id');
        $adminId = Session::get('admin_id');

        if ($role === 'admin') {
            $user = DB::table('admin_access')->where('id', $adminId)->first();
        } elseif ($role === 'employee') {
            $user = DB::table('allemployees')->where('id', $userId)->first();
            dd(DB::table('allemployees')->where('id', $userId)->first());

        } elseif ($role === 'client') {
            $user = DB::table('clients')->where('id', $userId)->first();
        } else {
            return back()->withErrors('User not found.');
        }

        // Validate the old password
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors('The old password is incorrect.');
        }

        // Update the password
        $newPasswordHash = Hash::make($request->new_password);
        if ($role === 'admin') {
            DB::table('admin_access')->where('id', $adminId)->update(['password' => $newPasswordHash]);
        } elseif ($role === 'employee') {
            DB::table('allemployees')->where('id', $userId)->update(['password' => $newPasswordHash]);
        } else {
            DB::table('clients')->where('id', $userId)->update(['password' => $newPasswordHash]);
        }
      
        return back()->with('success', 'Password updated successfully.');
    }
    private function setAdminSession($admin)
{
    $adminBranch = $admin->branch_id ? DB::table('branches')->where('id', $admin->branch_id)->first() : null;
    
    Session::put([
        'admin_id' => $admin->id,
        'user_id' => $admin->id,  // Also set user_id for compatibility
        'role' => 'admin',
        'email' => $admin->email,
        'admin_name' => $admin->name,
        'hierarchy_id' => $admin->hierarchy_id,
        'branch_id' => $admin->branch_id,
        'branch_name' => $adminBranch ? $adminBranch->name : null,
        'branch_address' => $adminBranch ? $adminBranch->address : null,
        'authenticated' => true
    ]);
}
public function confirmPassword(Request $request, $id)
{
    $request->validate([
        'new_password' => 'required|min:8',
        'confirm_password' => 'required|same:new_password',
    ]);

    $role = Session::get('role');

    if ($role === 'admin') {
        DB::table('admin_access')->where('id', $id)->update([
            'password' => Hash::make($request->new_password),
        ]);
    } elseif ($role === 'employee') {
        DB::table('allemployees')->where('id', $id)->update([
            'password' => Hash::make($request->new_password),
        ]);
    } else {
        DB::table('clients')->where('id', $id)->update([
            'password' => Hash::make($request->new_password),
        ]);
    }

    return redirect()->route('login')->with('success', 'Password updated successfully.');
}


}