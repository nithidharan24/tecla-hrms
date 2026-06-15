<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    /**
     * Handle API Login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = null;
        $role = null;

        // 1. Check Admin
        $admin = DB::table('admin_access')->where('email', $request->email)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            if ($admin->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is inactive. Please contact administrator.'
                ], 403);
            }
            $user = $admin;
            $role = 'admin';
        }

        // 2. Check Employee
        if (!$user) {
            $employee = DB::table('allemployees')->where('email', $request->email)->first();
            if ($employee && Hash::check($request->password, $employee->password)) {
                $user = $employee;
                
                // Determine HR role or standard employee
                $hierarchy = DB::table('hierarchies')->where('id', $employee->hierarchy_id)->first();
                $hierarchyLevel = $hierarchy ? strtolower(trim($hierarchy->hierarchy_level)) : '';
                
                if ($hierarchyLevel === 'hr' || $hierarchyLevel === 'hr manager') {
                    $role = 'hr';
                } else {
                    $role = 'employee';
                }
            }
        }

        // 3. Check Client
        if (!$user) {
            $client = DB::table('clients')->where('email', $request->email)->first();
            if ($client && Hash::check($request->password, $client->password)) {
                $user = $client;
                $role = 'client';
            }
        }

        // If credentials are valid, return success response
        if ($user) {
            // Generate a token. For a complete api token implementation,
            // you can save this in a token column, or use Sanctum/JWT.
            // Here we generate a simple secure token for the app.
            $token = bin2hex(random_bytes(30));

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'role' => $role,
                'user' => [
                    'id' => $user->id,
                    'name' => $role === 'admin' ? $user->name : ($role === 'client' ? $user->name : ($user->firstname . ' ' . $user->lastname)),
                    'email' => $user->email,
                    'employee_id' => $role === 'employee' || $role === 'hr' ? ($user->employeeid ?? null) : null,
                    'profile_img' => ($role === 'employee' || $role === 'hr') && !empty($user->profile_image)
                        ? asset($user->profile_image)
                        : null,
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.'
        ], 401);
    }

    /**
     * Handle API Forgot Password.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $admin = DB::table('admin_access')->where('email', $request->email)->first();
        $employee = DB::table('allemployees')->where('email', $request->email)->first();
        $client = DB::table('clients')->where('email', $request->email)->first();

        if (!$admin && !$employee && !$client) {
            return response()->json([
                'success' => false,
                'message' => 'Email address not found.'
            ], 404);
        }

        $user = $admin ?? $employee ?? $client;
        $role = $admin ? 'admin' : ($employee ? 'employee' : 'client');

        // Generate a new random password
        $newPassword = Str::random(10);
        $hashedPassword = Hash::make($newPassword);

        // Update password in the database
        if ($role === 'admin') {
            DB::table('admin_access')->where('email', $request->email)->update(['password' => $hashedPassword]);
        } elseif ($role === 'employee') {
            DB::table('allemployees')->where('email', $request->email)->update(['password' => $hashedPassword]);
        } else {
            DB::table('clients')->where('email', $request->email)->update(['password' => $hashedPassword]);
        }

        // Send the new password via email
        try {
            Mail::send([], [], function ($message) use ($user, $newPassword) {
                $message->to($user->email)
                    ->subject('Your New Password')
                    ->html("Hello, your new password is: <strong>{$newPassword}</strong>. Please log in and change it as soon as possible.");
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password reset successful, but failed to send the email. Please contact support.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'A new password has been successfully sent to your email.'
        ], 200);
    }

    /**
     * Handle API Change Password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'role' => 'required|in:admin,employee,hr,client',
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        $role = $request->role;
        $userId = $request->user_id;

        // Find the user depending on their role
        if ($role === 'admin') {
            $user = DB::table('admin_access')->where('id', $userId)->first();
        } elseif ($role === 'employee' || $role === 'hr') {
            $user = DB::table('allemployees')->where('id', $userId)->first();
        } elseif ($role === 'client') {
            $user = DB::table('clients')->where('id', $userId)->first();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role specified.'
            ], 400);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Validate the old password
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The old password is incorrect.'
            ], 400);
        }

        // Update the password in database
        $newPasswordHash = Hash::make($request->new_password);
        if ($role === 'admin') {
            DB::table('admin_access')->where('id', $userId)->update(['password' => $newPasswordHash]);
        } elseif ($role === 'employee' || $role === 'hr') {
            DB::table('allemployees')->where('id', $userId)->update(['password' => $newPasswordHash]);
        } else {
            DB::table('clients')->where('id', $userId)->update(['password' => $newPasswordHash]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ], 200);
    }
}
