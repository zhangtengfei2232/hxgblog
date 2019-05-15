<?php
/**
 * 验证公共函数
 */

/**
 * @param $data
 * @return bool
 */
function emptyArray($data)
{
    foreach ($data as $key => $datum) {
        if($key == 'sex') continue;
        if(empty($datum)) return true;
    }
    return false;
}

/**
 * @param $data
 * @return mixed
 */
function validateUserInformation($data)
{
    if(emptyArray($data)) return responseState(1,'你填写的个人信息不全');
    if(strlen($data['nick_name']) > 18) return responseState(1,'你填写的昵称过长');
    if(strlen ($data['phone']) != 11
        || ! preg_match ( '/^1[3|4|5|8][0-9]\d{4,8}$/', $data['phone'])){
        return responseState(1,'你填写的手机号不对');
    }
    if(!filter_var ($data['email'], FILTER_VALIDATE_EMAIL))
        return responseState(1,'你填写邮箱不合法');
    if(array_key_exists('password', $data)){
        if(strlen($data['password']) > 20) return responseState(1,'你填写的密码过长');
    }
    if(strlen($data['introduce']) > 60) return responseState(1,'你填写的自我介绍过长');
    return responseState(0,'验证通过');
}

/**判断文件上传是否合法
 * @param $file
 * @param $status
 * @return mixed
 */
function judgeReceiveFiles($file, $status = true)
{
    if(!$file->isValid()){
        return responseState(1,'上传失败,请重新上传');
    }
    if($status){
        $file_ypes = array('jpg', 'JPG', 'png', 'PNG', 'jpeg', 'JPEG');
        $isInFileType = in_array($file->getClientOriginalExtension(), $file_ypes);
        if(!$isInFileType) return responseState(1,'你上传的图片不合法');
        return responseState(0,'验证通过');
    }
    $file_types = array('PDF','pdf', 'WORD', 'word');
    if(!in_array($file->getClientOriginalExtension(), $file_types))
        return responseState(1,'你上传的文件不合法');
    return responseState(0,'验证通过');
}

