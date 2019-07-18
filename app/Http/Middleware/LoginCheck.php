<?php

namespace App\Http\Middleware;

use Closure;

class LoginCheck
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
        if (empty(session('user')) && empty(session('admin'))) {
            return responseToJson(2,'你未登录');
        }
        return $next($request);
    }
}
