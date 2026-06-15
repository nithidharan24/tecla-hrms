<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CheckTokenAndTenant
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
        if (!session()->has('token')) {
            session()->flash('messageType', 'error');
            session()->flash('message', 'Please log in first.');

            return redirect()->route('login');
        }

        // $role = session('role');
        // $tenantId = session('tenant_id');

        // if ($role === 'master_admin') {
        //     if(!session()->has('user_id')){
        //         session()->flash('messageType', 'error');
        //         session()->flash('message', 'Please log in first.');
        //         return redirect()->route('login');
        //     }
        //     return $next($request);
        // } else {
        //     if (!$tenantId || !DB::table('tenants')->where('tenant_id', $tenantId)->exists()) {
        //         session()->flash('messageType', 'error');
        //         session()->flash('message', 'Invalid access!');

        //         return redirect()->route('login');
        //     }
        // }

        return $next($request);
    }

}
