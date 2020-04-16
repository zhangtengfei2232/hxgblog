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
        $bai_du_login_cg = config('baidu');
        $param = array(
            'grant_type' => $bai_du_login_cg['grant_type'],
            'code' => $request->code,
            'client_id' => $bai_du_login_cg['client_id'],
            'client_secret' => $bai_du_login_cg['client_secret'],
            'redirect_uri' => $bai_du_login_cg['redirect_uri'],
        );
        $url = $bai_du_login_cg['access_token_url'];
        $token = json_decode(getHttpResponsePOST($url, $param), true);
        $access_token = $token['access_token'];
        Log::info('ccccc' . $access_token);
        if (! empty($access_token)) {
            $url = $bai_du_login_cg['user_info_url'] . $access_token;
            $info = getHttpResponseGET($url);
            Log::info($info);
        }


    }

}
