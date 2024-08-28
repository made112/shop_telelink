<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Route;

class hasPermission
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
        if (Auth::check() && Auth::user()->user_type == 'staff') {
            if (get_role_controller(class_basename(Route::current()->controller)) != null){
                if(in_array(get_role_controller(class_basename(Route::current()->controller)), json_decode(Auth::user()->staff->role->permissions))) {
                    return $next($request);
                } else {
                    abort(401);
                }
            }
        }
        return $next($request);
    }
}
