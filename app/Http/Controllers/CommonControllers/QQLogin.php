<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Model\Users;
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
        $qq_login_cg = config('qq');
        $param = array(
            'grant_type'    => $qq_login_cg['grant_type'],
            'code'          => $request->code,
            'client_id'     => $qq_login_cg['client_id'],
            'client_secret' => $qq_login_cg['client_secret'],
            'redirect_uri'  => $qq_login_cg['redirect_uri'],
        );
        $token_url = $qq_login_cg['access_token_url'] . http_build_query($param);
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
            echo '获取的access_token信息为空,稍后重试';
            exit;
        }
        $access_token = $access_token_info['access_token'];
        $get_openid_url = $qq_login_cg['openid_url'] . $access_token;
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
        //通过 access_token 判断是否已经注册
        $user_tag = $access_token . '_' . $openid_data['openid'];
        $user = Users::getThirdPartyUserData($user_tag);
        if (! $user->isEmpty()) {
            $user = updateLoginAuth(false, Users::LOGIN_WAY_SMS, $user);
            return responseToJson(0, '登录成功', $user);
        }
        $get_info_param = array(
            'access_token'       => $access_token,
            'oauth_consumer_key' => '101849190',
            'openid'             => $openid_data['openid']
        );
        $url       = $qq_login_cg['user_info_url'] . http_build_query($get_info_param);
        $user_info = json_decode(getHttpResponseGET($url), true);
        if (empty($user_info)) {
            echo '获取信息为空！稍后重试';
            exit;
        }
        $download_head_portrait = downloadHeadPortrait($user_info['figureurl_qq'], Users::QQ_FIELD, Users::QQ_HD_PT_EXT_NAME);
        if ($download_head_portrait === false) {
            echo '保存头像失败！，请重试!';
            exit;
        }
        $user_info['figureurl_qq'] = $download_head_portrait;
        $user_info['access_token'] = $user_tag;
        $user_info = $this->_dealFormatData($user_info);
        $add_user  = Users::addUserData($user_info, Users::QQ);
        if ($add_user) {
            redirect()->to(FRONT_END_URL . session('frontend_path')); //跳转到当时前端登录页面
            return true;
        }
        deleteFile($download_head_portrait, HEAD_PORTRAIT_FOLDER_NAME);
        echo '保存信息失败,稍后重试';
        return false;
    }

    /**
     * 处理QQ返回的用户信息
     * @param $user_info
     * @return array
     */
    private function _dealFormatData($user_info)
    {
        return array(
            'nick_name'      => $user_info['nickname'],
            'head_portrait'  => $user_info['figureurl_qq'],
            'third_party_id' => implode('_', array(Users::QQ, Users::selectUserNum())),
            'sex'            => ($user_info['gender'] == '男') ? 0 : 1,
            'register_way'   => Users::QQ,
            'login_way'      => Users::LOGIN_WAY_THIRD_PARTY,
            'access_token'   => $user_info['access_token']
        );

    }

}
