<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Model\Users;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 新用户注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerUser(Request $request)
    {
        $data['nick_name']  = $request->nickName;
        $data['sex']        = $request->sex;
        $data['phone']      = $request->phone;
        $data['email']      = $request->emails;
        $data['password']   = $request->password;
        $data['introduce']  = $request->introduce;
        if(Users::isPhoneExist($request->phone)) return responseToJson(1,'用户手机号已存在');
        $judge_data = validateUserInformation($data);
        if($judge_data['code'] == 1)      return responseToJson(1,$judge_data['msg']);
        $head_portrait = $request->file('headPortrait');
        $judge_img = judgeReceiveFiles($head_portrait);
        if($judge_img['code'] == 1)       return responseToJson(1,$judge_img['msg']);
        $disk = config('upload.head_portrait');
        try{
            $upload_img_road = uploadFile($head_portrait, $disk);
            if($upload_img_road['code'] == 1) return responseToJson(1,'添加用户信息失败');
            $data['head_portrait'] = $upload_img_road['data'];
            $data['role'] = 3;                                //默认为普通用户
            Users::addUserData($data);
            return responseToJson(0,'注册成功');
        }catch (\Exception $e){
            if(!empty($upload_img_road)) deleteFile($upload_img_road, $disk);
            return responseToJson(1,'注册失败');
        }
    }
    /**
     * 获取用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInformation(Request $request)
    {
        ($request->has('user_phone')) ? $user_phone = $request->user_phone : $user_phone = session('user')->phone;
        return responseToJson(0,'查询成功',Users::getUserInformationData($user_phone));
    }
    /**
     * 修改用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserInformation(Request $request)
    {
        $data['nick_name']  = $request->nickName;
        $data['sex']        = $request->sex;
        $data['phone']      = $request->phone;
        $data['email']      = $request->emails;
        $data['introduce']  = $request->introduce;
        if(Users::isNickNameExist($data['nick_name'],$data['phone'])) return responseToJson(1,'用户昵称已存在');
        $judge_data = validateUserInformation($data);
        if($judge_data['code'] == 1) return responseToJson(1,$judge_data['msg']);
        $disk = config('upload.head_portrait');
        try{
            //用户没有更改了头像
            if(!$request->hasFile('headPortrait')){
                Users::updateUserInformationData($data);
                return responseToJson(0,'修改成功666',$data);
            }
            //用户修改头像
            $head_portrait_file = $request->headPortrait;
            $judge_file = judgeReceiveFiles($head_portrait_file);
            if($judge_file['code'] == 1)      return responseToJson(1,$judge_file['msg']);
            $upload_img_road = uploadFile($head_portrait_file, $disk);
            if($upload_img_road['code'] == 1) return responseToJson(1,'修改信息失败');
            //图片上传成功的后续操作
            $old_head_portrait_road = Users::selectOldHeadPortrait($data['phone']);
            $data['head_portrait'] = $upload_img_road['data'];
            Users::updateUserInformationData($data);
            deleteFile($old_head_portrait_road, $disk);           //删除以前的用户文件
            return responseToJson(0,'修改成功',$data);
        } catch (\Exception $e){
            if(!empty($upload_img_road)) deleteFile($upload_img_road, $disk);
            return responseToJson(1,'修改信息失败');
        }
    }
}