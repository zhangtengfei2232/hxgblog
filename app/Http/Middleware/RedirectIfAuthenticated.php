<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (! Auth::guard($guard)->check()) {
            return responseToJson(3,'非法用户');
        }
        $response = $next($request);
        //验证token是否过期
        $user = Auth::guard($guard)->user();
        if (isTimeGreater($user->updated_token_at)){
            $response->header("api_token", $user->generateToken());
        }

        return $next($request);
    }
}
