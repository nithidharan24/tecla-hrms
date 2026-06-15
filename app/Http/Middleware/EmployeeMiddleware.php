<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and has employee role
        if (!Session::get('is_authenticated') || Session::get('role') !== 'employee') {
            Session::flush();
            return redirect()->route('login')->with('error', 'Unauthorized access. Please login as an employee.');
        }

        // Check if session has required employee data
        if (!Session::get('employee_id') || !Session::get('user_id')) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Invalid session. Please login again.');
        }

        // Optional: Check session timeout (24 hours)
        $loginTime = Session::get('login_time');
        if ($loginTime && now()->diffInHours($loginTime) > 24) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        return $next($request);
    }
}