<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Employee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // https://laracasts.com/discuss/channels/general-discussion/create-middleware-to-auth-admin-users?page=0
        if (Auth::user() && Auth::user()->role=='employee' || Auth::user()->role=='admin' ) {
            return $next($request);
          }
          return back()->with('error', 'Permisos insuficientes');
    }
}
