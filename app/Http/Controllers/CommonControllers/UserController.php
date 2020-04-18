<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Model\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * 新用户注册
     * @param Request $request
     * @return JsonResponse
     */
    public function registerUser(Request $request)
    {
        $data['nick_name']        = $request->nickName;
        $data['sex']              = $request->sex;
        $data['phone']            = $request->phone;
        $data['email']            = $request->emails;
        $data['password']         = $request->password;
        $data['introduce']        = $request->introduce;
        $data['api_token']        = str_random(128);
        $data['updated_token_at'] = millisecond();
        $data['register_way']     = Users::ACT_NUM_PWD;
        if (Users::isPhoneExist($request->phone)) {
            return responseToJson(1,'用户手机号已存在');
        }
        $judge_data = validateUserInformation($data);
        if ($judge_data['code'] == 1) {
            return responseToJson(1, $judge_data['msg']);
        }
        $head_portrait = $request->file('headPortrait');
        $judge_img = judgeReceiveFiles($head_portrait);
        if ($judge_img['code'] == 1) {
            return responseToJson(1, $judge_img['msg']);
        }
        try {
            $upload_img_road = uploadFile($head_portrait, HEAD_PORTRAIT_FOLDER_NAME);
            if ($upload_img_road['code'] == 1) {
                return responseToJson(1, '添加用户信息失败');
            }
            $data['head_portrait'] = HEAD_PORTRAIT_URL . $upload_img_road['data'];
            $data['role'] = 3;                                     //默认为普通用户
            Users::addUserData($data);
            $user = updateLoginAuth(false, Users::LOGIN_WAY_ACT_NUM_PWD, $data['phone']);
            return responseToJson(0, '注册成功', $user);
        } catch (\Exception $e) {
            if (! empty($upload_img_road)) {
                deleteFile($upload_img_road, HEAD_PORTRAIT_FOLDER_NAME);
            }
            return responseToJson(1, '注册失败');
        }
    }
    /**
     * 获取用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserInformation(Request $request)
    {
        ($request->has('user_phone')) ? $user_phone = $request->user_phone : $user_phone = session('user')->phone;
        return responseToJson(0,'查询成功',Users::getUserInformationData($user_phone));
    }
    /**
     * 修改用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function updateUserInformation(Request $request)
    {
        $data['nick_name']  = $request->nickName;
        $data['sex']        = $request->sex;
        $data['phone']      = $request->phone;
        $data['email']      = $request->emails;
        $data['introduce']  = $request->introduce;
        if (Users::isNickNameExist($data['nick_name'], $data['phone'])) {
            return responseToJson(1,'用户昵称已存在');
        }
        $judge_data = validateUserInformation($data);
        if ($judge_data['code'] == 1) {
            return responseToJson(1, $judge_data['msg']);
        }
        try {
            //用户没有更改了头像
            if (! $request->hasFile('headPortrait')) {
                Users::updateUserInformationData($data);
                return responseToJson(0,'修改成功', $data);
            }
            //用户修改头像
            $head_portrait_file = $request->headPortrait;
            $judge_file = judgeReceiveFiles($head_portrait_file);
            if ($judge_file['code'] == 1) {
                return responseToJson(1, $judge_file['msg']);
            }
            $upload_img_road = uploadFile($head_portrait_file, HEAD_PORTRAIT_FOLDER_NAME);
            if ($upload_img_road['code'] == 1) {
                return responseToJson(1, '修改信息失败');
            }
            //图片上传成功的后续操作
            $old_head_portrait_road = Users::selectOldHeadPortrait($data['phone']);
            $data['head_portrait'] = $upload_img_road['data'];
            Users::updateUserInformationData($data);
            deleteFile($old_head_portrait_road, HEAD_PORTRAIT_FOLDER_NAME);//删除以前的用户文件
            return responseToJson(0, '修改成功', $data);
        } catch (\Exception $e) {
            if (! empty($upload_img_road)) {
                deleteFile($upload_img_road, HEAD_PORTRAIT_FOLDER_NAME);
            }
            return responseToJson(1, '修改信息失败');
        }
    }

    /**登录后修改用户密码
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request)
    {

        if ($request->role == "admin") {
            if (! session()->has('admin')) {
                return responseToJson(2, '请重新登录');
            }
            $user = session('admin');
        } else {
            if (! session()->has('user')) {
                return responseToJson(2, '请重新登录');
            }
            $user = session('user');
        }
        return Users::updatePassword($user->phone, $request->new_password) ? responseToJson(0,'修改密码成功')
                : responseToJson(1,'修改密码失败');
    }

    /**
     * 用户忘记密码，根据短信验证码修改密码
     * @param Request $request
     * @return JsonResponse
     */
    public function byCodeUpdatePassword(Request $request)
    {
        $sms_code = $request->sms_code;
        $phone    = $request->phone;
        $validateSms = validateSmsLogin($sms_code, $phone);
        if ($validateSms['code'] == 1) {
            return responseToJson(1, $validateSms['msg']);
        }
        return Users::updatePassword($phone, $request->new_password) ? responseToJson(0,'修改密码成功') : responseToJson(1,'修改密码失败');
    }
}
