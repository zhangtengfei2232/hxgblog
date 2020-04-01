<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AliPayLogin extends Controller
{
    public function aliPayLoginCallBack(Request $request)
    {
        Log::info($request->auth_code);
        if (!$request->has('auth_code')) {
            echo '获取app_auth_code失败,请重试';
            exit;
        }
        //获取app_auth_token
        $ali_pay_login_cg = config('alipay')['login'];
        $base_param = array(
            'app_id'     => $ali_pay_login_cg['app_id'],
            'grant_type' => $ali_pay_login_cg['grant_type'],
            'charset'    => $ali_pay_login_cg['charset'],
            'sign_type'  => $ali_pay_login_cg['sign_type'],
            'version'    => $ali_pay_login_cg['version'],
        );
        $get_token_param = array_merge($base_param, array(
            'method'     => $ali_pay_login_cg['get_token_api'],
            'code'       => $request->auth_code,
            'timestamp'  => date('Y-m-d H:i:s')
        ));
        Log::info('canshu' . json_encode($get_token_param));
        $signStr = aliPayParamToString($get_token_param);
        Log::info('str :' . $signStr);
        $rsa = enRSA2($signStr);
        Log::info('rsa' . $rsa);
        $sign = urlencode($rsa);
        $query = $signStr . '&sign=' . $sign;
        $url = $ali_pay_login_cg['base_url'] . $query;
        $access_token = getHttpResponsePOST($ali_pay_login_cg['base_url'], $query);
        Log::info('token' . $url . '-----------------------' . json_encode($access_token));
        $access_token_info = json_decode($access_token, true);
        if (!isset($access_token_info['alipay_system_oauth_token_response'])) {
            echo '获取token信息失败';
            exit;
        }
        $access_token = $access_token_info['alipay_system_oauth_token_response']['access_token'];
        //请求用户信息
        $get_info_param = array_merge($base_param, array(
                'method'     => $ali_pay_login_cg['user_info_api'],
                'timestamp'  => date('Y-m-d H:i:s'),
                'auth_token' => $access_token,
        ));
        $signStr = aliPayParamToString($get_info_param);
        $rsaStr = enRSA2($signStr);
        $sign = urlencode($rsaStr);
        $query = $signStr . '&sign=' . $sign;
        Log::info($ali_pay_login_cg['base_url'] . '?' . $query);
        $user_info = getHttpResponsePOST($ali_pay_login_cg['base_url'], $query);
        $user_info = method($user_info, 'utf-8', 'gbk');
        Log::info('info' . $user_info);
        $user_info = json_decode($user_info, true);
        dd($user_info);




    }

}
