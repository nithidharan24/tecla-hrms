<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    public function handle(Request $request, Closure $next)
    {
        if (empty(session('user_id'))) {
            return redirect()->route('login')
                ->with('error', 'Session expired. Please log in again.');
        }

        return $next($request);
    }
}
