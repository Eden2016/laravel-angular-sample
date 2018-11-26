<?php
namespace App\Http\Middleware;

use Auth;
use Route;
use Closure;

class CheckClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'client')
    {
        if($request->getRequestUri() == '/client/login') {
            return $next($request);
        }

        if (!Auth::guard($guard)->check()) {
            return redirect('/client/login');
        }

        return $next($request);
    }
}
