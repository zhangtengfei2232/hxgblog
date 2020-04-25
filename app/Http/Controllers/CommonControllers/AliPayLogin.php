<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Model\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AliPayLogin extends Controller
{
    public function aliPayLoginCallBack(Request $request)
    {
        if (! $request->has('auth_code')) {
            echo '获取app_auth_code失败,请重试';
            exit;
        }
        //获取app_auth_token
        $base_param = array(
            'app_id'     => ALI_PAY_CLIENT_ID,
            'grant_type' => GRANT_TYPE,
            'charset'    => ALI_PAY_CHART_SET,
            'sign_type'  => ALI_PAY_SIGN_TYPE,
            'version'    => ALI_PAY_VERSION,
        );
        $get_token_param = array_merge($base_param, array(
            'method'     => ALI_PAY_TOKEN_API,
            'code'       => $request->input('auth_code'),
            'timestamp'  => date('Y-m-d H:i:s')
        ));
        Log::info('canshu' . json_encode($get_token_param));
        $signStr = aliPayParamToString($get_token_param);
        Log::info('str :' . $signStr);
        $rsa = enRSA2($signStr);
        Log::info('rsa' . $rsa);
        $sign = urlencode($rsa);
        $query = $signStr . '&sign=' . $sign;
        $url = ALI_PAY_ICE_BASE_URL . $query;
        $access_token = getHttpResponsePOST(ALI_PAY_ICE_BASE_URL, $query);
        Log::info('token' . $url . '-----------------------' . json_encode($access_token));
        $access_token_info = json_decode($access_token, true);
        if (! isset($access_token_info['alipay_system_oauth_token_response'])) {
            echo '获取token信息失败';
            exit;
        }
        $access_token = $access_token_info['alipay_system_oauth_token_response']['access_token'];

        //通过 access_token 判断是否已经注册
        $user = Users::getThirdPartyUserData($access_token);
        if (! $user->isEmpty()) {
            $user = updateLoginAuth(false, Users::LOGIN_WAY_SMS, $user);
            return responseToJson(0, '登录成功', $user);
        }

        //请求用户信息
        $get_info_param = array_merge($base_param, array(
                'method'     => ALI_PAY_USER_INFO_API,
                'timestamp'  => date('Y-m-d H:i:s'),
                'auth_token' => $access_token,
        ));
        $signStr = aliPayParamToString($get_info_param);
        $rsaStr = enRSA2($signStr);
        $sign = urlencode($rsaStr);
        $query = $signStr . '&sign=' . $sign;
        Log::info(ALI_PAY_ICE_BASE_URL . '?' . $query);
        $user_info = getHttpResponsePOST(ALI_PAY_ICE_BASE_URL, $query);
        $user_info = mb_convert_encoding($user_info, 'utf-8', 'gbk');
        Log::info('info' . $user_info);
        $user_info = json_decode($user_info, true);
        if (! empty($user_info['alipay_user_info_share_response'])) {
            echo '获取信息为空！稍后重试';
            exit;
        }
        //下载头像到本地
        $download_head_portrait = downloadHeadPortrait($user_info['avatar'], Users::ALI_PAY_FIELD, Users::ALI_PAY_HD_PT_EXT_NAME);
        if ($download_head_portrait === false) {
            echo '保存头像失败！，请重试!';
            exit;
        }
        $user_info['avatar'] = $download_head_portrait;
        $user_info['access_token'] = $access_token;
        $user_info = $this->_dealFormatData($user_info['alipay_user_info_share_response']);
        $add_user  = Users::addUserData($user_info, Users::ALI_PAY);
        if ($add_user) {
            return redirect()->to(session('frontend_url')); //跳转到当时前端登录页面
        }
        deleteFile($download_head_portrait, HEAD_PORTRAIT_FOLDER_NAME);
        echo '保存信息失败,稍后重试';
        return false;
    }


    /**
     * 处理支付宝返回的用户信息
     * @param $user_info
     * @return array
     */
    private function _dealFormatData($user_info)
    {
        return array(
            'nick_name'      => $user_info['nickname'],
            'head_portrait'  => $user_info['avatar'],
            'third_party_id' => $user_info['user_id'],
            'sex'            => ($user_info['gender'] == 'm') ? 0 : 1,
            'register_way'   => Users::ALI_PAY,
            'access_token'   => $user_info['access_token']
        );
    }

}
