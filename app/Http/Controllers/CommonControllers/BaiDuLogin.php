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
        Log::info('zzz:' . $request->code);
        if (! $request->has('code')) {
            echo '没有获取到code';
            exit;
        }
        $bai_du_login_cg = config('baidu');
        $param = array(
            'grant_type'    => $bai_du_login_cg['grant_type'],
            'code'          => $request->code,
            'client_id'     => $bai_du_login_cg['client_id'],
            'client_secret' => $bai_du_login_cg['client_secret'],
            'redirect_uri'  => $bai_du_login_cg['redirect_uri'],
        );
        $url = $bai_du_login_cg['access_token_url'];
        $token = json_decode(getHttpResponsePOST($url, $param), true);
        $access_token = $token['access_token'];
        Log::info('ccccc' . $access_token);
        if (empty($access_token)) {
            echo '获取 access_token 失败！建议稍后重试！';
            exit;
        }
        //通过 access_token 判断是否已经注册
        $user = Users::getThirdPartyUserData($access_token);
        if (! $user->isEmpty()) {
            $user = updateLoginAuth(false, Users::LOGIN_WAY_SMS, $user);
            return responseToJson(0, '登录成功', $user);
        }

        $url = $bai_du_login_cg['user_info_url'] . $access_token;
        $user_info = json_decode(getHttpResponseGET($url), true);
        if (empty($user_info)) {
            echo '获取信息为空！稍后重试';
            exit;
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
        $add_user  = Users::addUserData($user_info, Users::ALI_PAY);
        if ($add_user) {
            redirect()->to(FRONT_END_URL . session('frontend_path')); //跳转到当时前端登录页面
            return true;
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
            'login_way'      => Users::LOGIN_WAY_THIRD_PARTY,
            'access_token'   => $user_info['access_token']
        );

    }

}
