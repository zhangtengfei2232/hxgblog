<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeiBoLogin extends Controller
{

    public function weiBoOAuthCallBack(Request $request)
    {
        if (! $request->has('code')) {
            echo '没有获取到code';
            exit;
        }
        $wei_bo_login_cg = config('weibo')['login'];
        $param = array(
            'client_id' => $wei_bo_login_cg['client_id'],
            'client_secret' => $wei_bo_login_cg['client_secret'],
            'grant_type' => $wei_bo_login_cg['grant_type'],
            'redirect_uri' => $wei_bo_login_cg['oauth_redirect_uri'],
            'code' => $request->code
        );
        $get_token_url = $wei_bo_login_cg['access_token_url'] . http_build_query($param);
        $token_info = json_decode(getHttpResponsePOST($get_token_url) ,true);
        if (isset($token_info['error'])) {
            echo '获取token失败 error_code: ' . $token_info['error_code'] . 'error_description: ' . $token_info['error_description'];
            exit;
        }
        if (empty($token_info['access_token']) || empty($token_info['uid'])) {
            echo '获取的token信息为空';
            exit;
        }
        //获取用户信息
        $get_user_info_param = array(
            'access_token' => $token_info['access_token'],
            'uid' => $token_info['uid']
        );
        $get_user_info_url = $wei_bo_login_cg['user_info_url'] . http_build_query($get_user_info_param);
        dd(json_decode(getHttpResponseGET($get_user_info_url), true));
    }

    public function weiBoCancelOAuthCallBack(Request $request)
    {
        Log::info('rsdsd');

    }

}
