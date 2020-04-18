<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Model\Users;
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
            'client_id'     => $wei_bo_login_cg['client_id'],
            'client_secret' => $wei_bo_login_cg['client_secret'],
            'grant_type'    => $wei_bo_login_cg['grant_type'],
            'redirect_uri'  => $wei_bo_login_cg['oauth_redirect_uri'],
            'code'          => $request->code
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

        //通过 access_token 判断是否已经注册
        $user = Users::getThirdPartyUserData($token_info['access_token']);
        if (! $user->isEmpty()) {
            $user = updateLoginAuth(false, Users::LOGIN_WAY_SMS, $user);
            return responseToJson(0, '登录成功', $user);
        }

        //获取用户信息
        $get_user_info_param = array(
            'access_token' => $token_info['access_token'],
            'uid' => $token_info['uid']
        );
        $get_user_info_url = $wei_bo_login_cg['user_info_url'] . http_build_query($get_user_info_param);
        $user_info = (json_decode(getHttpResponseGET($get_user_info_url), true));
        if (empty($user_info)) {
            echo '获取信息为空！稍后重试';
            exit;
        }
        $download_head_portrait = downloadHeadPortrait($user_info['avatar_hd'], Users::WEI_BO_FIELD, Users::WEI_BO_HD_PT_EXT_NAME);
        if ($download_head_portrait === false) {
            echo '保存头像失败！，请重试!';
            exit;
        }
        $user_info['avatar_hd'] = $download_head_portrait;
        $user_info['access_token'] = $token_info['access_token'];
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

    public function weiBoCancelOAuthCallBack(Request $request)
    {
        Log::info('rsdsd');

    }

    /**
     * 处理QQ返回的用户信息
     * @param $user_info
     * @return array
     */
    private function _dealFormatData($user_info)
    {
        return array(
            'nick_name'      => empty($user_info['nickname']) ? Users::DEFAULT_NICK_NAME_PREFIX_FIELD . Users::selectUserNum() : $user_info['name'],
            'head_portrait'  => $user_info['avatar_hd'],
            'third_party_id' => implode('_', array(Users::WEI_BO, $user_info['id'])),
            'sex'            => ($user_info['gender'] == 'm') ? 0 : 1,
            'register_way'   => Users::WEI_BO,
            'login_way'      => Users::LOGIN_WAY_THIRD_PARTY,
            'introduce'      => empty($user_info['description']) ? '' : $user_info['description'],
            'access_token'    => $user_info['access_token']
        );
    }

}
