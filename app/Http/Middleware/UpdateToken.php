<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @param $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard)
    {
        $response = $next($request);
        //验证token是否过期
        $user = Auth::guard($guard)->user();
        if (isTimeGreater($user->updated_token_at)){
            $response->header("api_token", $user->generateToken());
        }
        return $response;
    }
}
