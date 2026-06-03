<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        $userRole = auth()->user()->role;

        // If the user's role is not in the allowed roles list, redirect them to their own dashboard
        if (!in_array($userRole, $roles)) {
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'technician') {
                return redirect()->route('tech.dashboard');
            } elseif ($userRole === 'employee') {
                return redirect()->route('employee.dashboard');
            }

            return redirect('/');
        }

        return $next($request);
    }
}
