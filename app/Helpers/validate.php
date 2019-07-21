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

/**
 * 验证用户输入的评论内容
 * @param $comment_content
 * @return mixed
 */
function validateCommentContent($comment_content)
{
    if(strlen($comment_content) > 100) return responseState(1,'你输入的内容过长');
    if($comment_content != strip_tags($comment_content)) return responseState(1,'你输入的内容不合法！');
    return responseState(0,'验证通过');
}

/**判断文件上传是否合法
 * @param $file
 * @param $status
 * @return mixed
 */
function judgeReceiveFiles($file, $status = 1)
{
    if(!$file->isValid()){
        return responseState(1,'上传失败,请重新上传');
    }
    if($status == 1){
        $file_ypes = array('jpg', 'JPG', 'png', 'PNG', 'jpeg', 'JPEG');
        if(!in_array($file->getClientOriginalExtension(), $file_ypes))
            return responseState(1,'你上传的图片不合法');
    }elseif ($status == 2){
        $file_types = array('mp3','MP3');
        if(!in_array($file->getClientOriginalExtension(), $file_types))
            return responseState(1,'你上传的文件不合法');
    }elseif ($status == 3){
        $file_types = array('PDF','pdf', 'WORD', 'word');
        if(!in_array($file->getClientOriginalExtension(), $file_types))
            return responseState(1,'你上传的文件不合法');
    }
    return responseState(0,'验证通过');
}

/**
 * 验证多个文件
 * @param $file_data
 * @return bool
 */
function judgeMultipleFile($file_data)
{
    foreach ($file_data as $file){
        if(judgeReceiveFiles($file)['code'] == 1) return false;
    }
    return true;
}

/**
 * 验证文章信息
 * @param $data
 * @return mixed
 */
function validateArticalData($data)
{
    if(emptyArray($data)) return responseState(1,'文章信息填写不完整');
    if(strlen($data['arti_title']) > 30) responseState(1,'文章题目过长');
    if($data['arti_title'] != strip_tags($data['arti_title'])) return responseState(1,'你输入的文章题目不合法');
    if($data['arti_content'] != strip_tags($data['arti_content']))return responseState(1,'你输入的文章题目不合法');
    return responseState(0,'验证通过');
}

/**
 * 验证相册信息
 * @param $data
 * @return \Illuminate\Http\JsonResponse
 */
function validateAlbumData($data)
{
    if(emptyArray($data)) return responseState(1,'相册信息填写不完整');
    if(strlen($data['albu_name']) > 30) return responseState(1,'你填写的相册名字过长');
    if(strlen($data['albu_introduce']) > 200) return responseState(1,'你填写的相册介绍过长');
    return responseState(0,'验证通过');
}

/**
 * 验证相册密保信息
 * @param $data
 * @return mixed
 */
function validateAlbumSecSty($data)
{
    if(strlen($data['albu_question']) > 30) return responseState(1,'你填写的相册密保问题过长');
    if(strlen($data['albu_answer']) > 30) return responseState(1,'你填写的相册密保答案过长');
    return responseState(0,'验证通过');
}

/**
 * 验证展览内容是否合法
 * @param $data
 * @return mixed
 */
function validateExhibit($data)
{
    if(emptyArray($data)) return responseState(1,'你填写的不完整');
    if(strlen($data['exht_name']) > 100) return responseState(1,'你填写的名言名字过长');
    if(strlen($data['exht_content']) > 220) return responseState(1,'你填写的名言内容过长');
    return responseState(0,'验证通过');

}

