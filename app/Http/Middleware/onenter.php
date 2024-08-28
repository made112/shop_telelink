<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class onenter
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::id() == 12) {
            debugbar()->enable();
            Config::set('debugbar.enabled', true);
        } else {
            debugbar()->disable();
            Config::set('debugbar.enabled', false);
        }
        return $next($request);
    }
}
