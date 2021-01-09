<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      // https://laracasts.com/discuss/channels/general-discussion/create-middleware-to-auth-admin-users?page=0
        if (Auth::user() && Auth::user()->role=='admin') {
          return $next($request);
        }
        return redirect('login');
    }
}
