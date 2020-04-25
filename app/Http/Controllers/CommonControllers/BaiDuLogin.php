<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Model\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaiDuLogin extends Controller
{
    public function baiDuCallBack(Request $request)
    {
        if (! $request->has('code')) {
            echo '没有获取到code';
            exit;
        }
        $param = array(
            'grant_type'    => GRANT_TYPE,
            'code'          => $request->input('code'),
            'client_id'     => BAI_DU_CLIENT_ID,
            'client_secret' => BAI_DU_CLIENT_SECRET,
            'redirect_uri'  => BAI_DU_REDIRECT_URI,
        );
        $url = BAI_DU_ACCESS_TOKEN_URL;
        $token = json_decode(getHttpResponsePOST($url, $param), true);
        $access_token = $token['access_token'];
        Log::info('ccccc' . $access_token);
        if (empty($access_token)) {
            echo '获取 access_token 失败！建议稍后重试！';
            exit;
        }
        $url = BAI_DU_USER_INFO_URL . $access_token;
        $user_info = json_decode(getHttpResponseGET($url), true);
        if (empty($user_info)) {
            echo '获取信息为空！稍后重试';
            exit;
        }

        //判断是否已经注册过了
        //通过userid判断是否已经注册
        $user = Users::getThirdPartyUserData($user_info['userid'], 'third_party_id');
        if (! empty($user)) {
            updateLoginAuth(false, Users::LOGIN_WAY_THIRD_PARTY, $user->third_party_id, 'third_party_id');
            return redirect()->to(session('frontend_url')); //跳转到当时前端登录页面
        }

        //下载头像到本地
        $head_portrait_url = BAI_DU_HEAD_PORTRAIT_BASE_URL . $user_info['portrait'];
        $download_head_portrait = downloadHeadPortrait($head_portrait_url, Users::BAI_DU_FIELD, Users::BAI_DU_HD_PT_EXT_NAME);
        if ($download_head_portrait === false) {
            echo '保存头像失败！，请重试!';
            exit;
        }
        $user_info['portrait'] = $download_head_portrait;
        $user_info['access_token'] = $access_token;
        $user_info = $this->_dealFormatData($user_info);
        $add_user  = Users::addUserData($user_info, Users::BAI_DU);
        if ($add_user) {
            updateLoginAuth(false, Users::LOGIN_WAY_THIRD_PARTY, $user_info['userid'], 'third_party_id');
            return redirect()->to(session('frontend_url')); //跳转到当时前端登录页面
        }
        deleteFile($download_head_portrait, HEAD_PORTRAIT_FOLDER_NAME);
        echo '保存信息失败,稍后重试';
        return false;

    }


    /**
     * 处理百度返回的用户信息
     * @param $user_info
     * @return array
     */
    private function _dealFormatData($user_info)
    {
        return array(
            'nick_name'      => $user_info['username'],
            'head_portrait'  => $user_info['portrait'],
            'third_party_id' => $user_info['userid'],
            'sex'            => 1,
            'register_way'   => Users::BAI_DU,
            'access_token'   => $user_info['access_token'],
            'role'           => 3
        );

    }

}
