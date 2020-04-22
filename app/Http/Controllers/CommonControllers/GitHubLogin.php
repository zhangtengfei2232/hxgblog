<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Model\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubLogin extends Controller
{
    public function gitHubCallBack(Request $request)
    {
        if (! $request->has('code')) {
            echo '获取code失败，请重试！';
            exit;
        }
        $param = array(
            'code'          => $request->input('code'),
            'client_id'     => GITHUB_CLIENT_ID,
            'client_secret' => GITHUB_CLIENT_SECRET,
        );
        $url = GITHUB_ACCESS_TOKEN_URL;
        //$content====>'A=XXXXX&B=XXXX', github以路由参数返回结果
        $content = getHttpResponsePOST($url, $param);         //获取access_token
        Log::info($content);
        $data = array();
        parse_str($content,$data);
        //请求用户信息
        if (empty($data['access_token'])) {
            echo '获取的access_token信息为空,稍后重试';
            exit;
        }

        //通过 access_token 判断是否已经注册
        $access_token = $data['access_token'];
        $user = Users::getThirdPartyUserData($access_token);
        if (! $user->isEmpty()) {
            $user = updateLoginAuth(false, Users::LOGIN_WAY_SMS, $user);
            return responseToJson(0, '登录成功', $user);
        }

        $info_url = GITHUB_USER_INFO_URL . $access_token;

        $headers[] = 'Authorization: token '. $access_token;
        $headers[] = "User-Agent: 坏小哥博客";
        $result = getHttpResponseGET($info_url, $headers);
        $user_info = json_decode($result, true);
        if (empty($user_info)) {
            echo '获取信息为空！稍后重试';
            exit;
        }
        $download_head_portrait = downloadHeadPortrait($user_info['avatar_url'], Users::GITHUB_FIELD, Users::GITHUB_HD_PT_EXT_NAME);
        if ($download_head_portrait === false) {
            echo '保存头像失败！，请重试!';
            exit;
        }
        $user_info['avatar_url']   = $download_head_portrait;
        $user_info['access_token'] = $access_token;
        $user_info = $this->_dealFormatData($user_info);
        $add_user  = Users::addUserData($user_info, Users::GITHUB);
        if ($add_user) {
            return redirect()->to(session('frontend_url')); //跳转到当时前端登录页面
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
            'nick_name'      => empty($user_info['name']) ? Users::DEFAULT_NICK_NAME_PREFIX_FIELD . Users::selectUserNum() : $user_info['name'],
            'head_portrait'  => $user_info['avatar_url'],
            'third_party_id' => implode('_', array(Users::WEI_BO, $user_info['id'])),
            'sex'            => ($user_info['gender'] == 'm') ? 0 : 1,
            'register_way'   => Users::GITHUB,
            'email'          => $user_info['email'],
            'access_token'   => $user_info['access_token']
        );
    }




}
