<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequest
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
        $response = $next($request);
        $origin  = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $allow_origin = [
            'https://blogback.zhangtengfei-steven.cn',
            'http://localhost:8080',
        ];
        if (in_array($origin, $allow_origin)) {
            $response->headers->add(['Access-Control-Allow-Origin' => $origin]);
            $response->headers->add(['Access-Control-Allow-Headers' => 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-Requested-With']);
            $response->headers->add(['Access-Control-Expose-Headers' => 'Authorization,authenticated,api_token']);
            $response->headers->add(['Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, OPTIONS, DELETE']);
            $response->headers->add(['Access-Control-Allow-Credentials' => 'true']);
        }
        return $response;
    }
}
