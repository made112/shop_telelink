<?php

namespace App\Http\Middleware;

use Closure;

class changeLocaleApi
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
        if($request->header('lang') and $request->header('lang') == 'ar'){
            app()->setlocale('sa');
        }else {
            app()->setlocale($request->header('lang', 'en'));
        }
        return $next($request);
    }
}
