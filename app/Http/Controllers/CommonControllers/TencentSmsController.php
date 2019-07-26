<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Model\Users;
use Illuminate\Http\Request;
use Qcloud\Sms\SmsSingleSender;

class TencentSmsController extends Controller
{
    /**
     * 发送短信登录验证码
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSmsCode(Request $request)
    {
        $phone         = $request->phone;
        if(! Users::validateUser($phone)) return responseToJson(1,'你是外星人吗？');
        $tencentConfig = config('tencent');
        $appid         = $tencentConfig['appid'];                     // 短信应用SDK AppID  1400开头
        $appkey        = $tencentConfig['appkey'];                    // 短信应用SDK AppKey
        $phoneNumbers  = [$phone];                                    // 需要发送短信的手机号码
        $templateId    = $tencentConfig['templateId'];                // 短信模板ID，需要在短信应用中申请
        $smsSign       = $tencentConfig['smsSign'];                   //签名参数使用的是`签名内容`，而不是`签名ID`
        // 指定模板ID单发短信
        try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $random_number = random_int(999,9999);                    //验证码随机数
            $effective_time = 1;                                      //有效时间
            $params = [$random_number, $effective_time];
            $result = $ssender->sendWithParam("86", $phoneNumbers[0], $templateId,
                $params, $smsSign, "", "");                // 签名参数未提供或者为空时，会使用默认签名发送短信
            if(json_decode($result)->errmsg == "OK"){
                session(['code_info' => $random_number . ',' . time() . ',' . $phone]);
                return responseToJson(0,'短信发送成功');
            }
            return responseToJson(1,'短信发送失败');
        } catch (\Exception $e) {
            return responseToJson(1,'短信发送失败');
        }
    }





}