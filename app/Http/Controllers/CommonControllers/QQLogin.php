<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QQLogin extends Controller
{
    public function qqCallBack(Request $request)
    {
        if (! $request->has('code')) {
            echo '没有获取到code';
            exit;
        }
        Log::info('zzz:' . $request->code);
        $qq_login_fg = config('qq');
        $param = array(
            'grant_type' => $qq_login_fg['grant_type'],
            'code' => $request->code,
            'client_id' => $qq_login_fg['client_id'],
            'client_secret' => $qq_login_fg['client_secret'],
            'redirect_uri' => $qq_login_fg['redirect_uri'],
        );
        $token_url = $qq_login_fg['access_token_url'] . http_build_query($param);
        $response = file_get_contents($token_url);
        Log::info(json_encode($response));
        if (strpos($response, "callback") !== false)
        {
            $error_msg = dealQQData($response);
            dealQQErrorMessage($error_msg, '获取token信息失败');
            exit;
        }
        $access_token_info = array();
        parse_str($response, $access_token_info);
        if (empty($access_token_info['access_token'])) {
            echo '获取的token信息为空,稍后重试';
            exit;
        }
        $access_token = $access_token_info['access_token'];
        $get_openid_url = $qq_login_fg['openid_url'] . $access_token;
        $openid_data = getHttpResponseGET($get_openid_url);
        Log::info('openid' . json_encode($openid_data));
        if (strpos($openid_data, "callback") === false) {
            echo '获取openid异常，稍后重试！';
            exit;
        }
        $openid_data = dealQQData($openid_data);
        if (isset($openid_data['error'])) {
            dealQQErrorMessage($openid_data, '获取openid失败');
            exit;
        }
        if (empty($openid_data['openid'])) {
            echo '获取的openid信息为空，请稍后重试';
            exit;
        }
        $get_info_param = array(
            'access_token' => $access_token,
            'oauth_consumer_key' => '101849190',
            'openid' => $openid_data['openid']
        );
        $url = $qq_login_fg['user_info_url'] . http_build_query($get_info_param);
        $info = json_decode(getHttpResponseGET($url), true);
        dd($info);
    }


}
