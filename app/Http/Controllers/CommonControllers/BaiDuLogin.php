<?php


namespace App\Http\Controllers\CommonControllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaiDuLogin extends Controller
{
    public function baiDuCallBack(Request $request)
    {
        Log::info('zzz:' . $request->code);
        if (! $request->has('code')) {
            echo '没有获取到code';
            exit;
        }
        $baidu_login_fg = config('baidu');
        $param = array(
            'grant_type' => $baidu_login_fg['grant_type'],
            'code' => $request->code,
            'client_id' => $baidu_login_fg['client_id'],
            'client_secret' => $baidu_login_fg['client_secret'],
            'redirect_uri' => $baidu_login_fg['redirect_uri'],
        );
        $url = $baidu_login_fg['access_token_url'];
        $token = json_decode(getHttpResponsePOST($url, $param), true);
        $access_token = $token['access_token'];
        Log::info('ccccc' . $access_token);
        if (!empty($access_token)) {
            $url = $baidu_login_fg['user_info_url'] . $access_token;
            $info = getHttpResponseGET($url);
            Log::info($info);
        }


    }

}