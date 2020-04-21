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
        if ($key == 'sex') {
            continue;
        }
        if (empty($datum)) {
            return true;
        }
    }
    return false;
}

/**
 * @param $data
 * @return mixed
 */
function validateUserInformation($data)
{
    if (emptyArray($data)) {
        return responseState(1,'你填写的个人信息不全');
    }
    if (strlen($data['nick_name']) > 18) {
        return responseState(1,'你填写的昵称过长');
    }
    if (strlen ($data['phone']) != 11
        || ! preg_match ( '/^1[3|4|5|8][0-9]\d{4,8}$/', $data['phone'])) {
        return responseState(1,'你填写的手机号不对');
    }
    if (! filter_var ($data['email'], FILTER_VALIDATE_EMAIL)) {
        return responseState(1,'你填写邮箱不合法');
    }
    if (array_key_exists('password', $data) && strlen($data['password']) > 20){
        return responseState(1,'你填写的密码过长');
    }
    if (strlen($data['introduce']) > 60) {
        return responseState(1,'你填写的自我介绍过长');
    }
    return responseState(0,'验证通过');
}

/**
 * 验证用户输入的评论内容
 * @param $comment_content
 * @return mixed
 */
function validateCommentContent($comment_content)
{
    if (strlen($comment_content) > 100) {
        return responseState(1,'你输入的内容过长');
    }
    if ($comment_content != strip_tags($comment_content)) {
        return responseState(1,'你输入的内容不合法');
    }
    return responseState(0,'验证通过');
}

/**判断文件上传是否合法
 * @param $file
 * @param $status
 * @return mixed
 */
function judgeReceiveFiles($file, $status = 1)
{
    if (!$file->isValid()) {
        return responseState(1,'上传失败,请重新上传');
    }
    if (strpos($file->getClientOriginalName()," ")) {
        return responseState(1,'你上传的图片文件名有空格!');
    }
    $type = $file->getClientOriginalExtension();
    switch ($status) {
        case 1:
            if (! in_array($type, array('jpg', 'JPG', 'png', 'PNG', 'jpeg', 'JPEG'))) {
                return responseState(1,'你上传的图片不合法');
            }
            break;
        case 2:
            if (! in_array($type, array('mp3','MP3'))) {
                return responseState(1,'你上传的音乐文件不合法');
            }
            break;
        case 3:
            if (! in_array($type, array('lrc'))) {
                return responseState(1,'你上传的歌词文件不合法');
            }
            break;
        case 4:
            if (! in_array($type, array('PDF','pdf', 'WORD', 'word'))) {
                return responseState(1,'你上传的文件不合法');
            }
            break;
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
        $judge_file = judgeReceiveFiles($file);
        if ($judge_file['code'] == 1) {
            return responseState(1, $judge_file['msg']);
        }
    }
    return responseState(0,'验证通过');
}

/**
 * 验证文章信息
 * @param $data
 * @return mixed
 */
function validateArticleData($data)
{
    if (emptyArray($data)) {
        return responseState(1,'文章信息填写不完整');
    }
    if(strlen($data['art_title']) > 200) {
        return responseState(1,'文章题目过长');
    }
    if ($data['art_title'] != strip_tags($data['art_title'])) {
        return responseState(1,'你输入的文章题目不合法');
    }
    if ($data['art_content'] != strip_tags($data['art_content'])) {
        return responseState(1,'你输入的文章题目不合法');
    }
    return responseState(0,'验证通过');
}

/**
 *  验证相册信息
 * @param $data
 * @return mixed
 */
function validateAlbumData($data)
{
    if (emptyArray($data)) {
        return responseState(1,'相册信息填写不完整');
    }
    if (strlen($data['alb_name']) > 30) {
        return responseState(1,'你填写的相册名字过长');
    }
    if (strlen($data['alb_introduce']) > 200) {
        return responseState(1,'你填写的相册介绍过长');
    }
    return responseState(0,'验证通过');
}

/**
 * 验证相册密保信息
 * @param $data
 * @return mixed
 */
function validateAlbumSecSty($data)
{
    if (strlen($data['alb_question']) > 30) {
        return responseState(1,'你填写的相册密保问题过长');
    }
    if (strlen($data['alb_answer']) > 30) {
        return responseState(1,'你填写的相册密保答案过长');
    }
    return responseState(0,'验证通过');
}

/**
 * 验证展览内容是否合法
 * @param $data
 * @return mixed
 */
function validateExhibit($data)
{
    if (emptyArray($data)) {
        return responseState(1,'你填写的不完整');
    }
    if (strlen($data['exh_name']) > 100) {
        return responseState(1,'你填写的名言名字过长');
    }
    if (strlen($data['exh_content']) > 220) {
        return responseState(1,'你填写的名言内容过长');
    }
    return responseState(0,'验证通过');
}

function validateSmsLogin($code, $phone)
{
    if (! session()->has('code_info')) {
        return responseState(1,'请你重新获取验证码');
    }
    $login_info = explode(',',session('code_info'));
    $send_code  = $login_info[0];                              //发送的验证码
    if ($send_code != $code) {
        return responseState(1,'你输入的验证码不正确');
    }
    $send_code_time = $login_info[1];                          //发送验证码的时间
    if (time() - $send_code_time > 60) {
        return responseState(1,'你需要重新获取验证码');
    }
    $send_phone = $login_info[2];                              //发送验证码的电话
    if ($send_phone != $phone) {
        return responseState(1,'你填写的手机号不正确');
    }
    return responseState(0,'验证通过');
}

