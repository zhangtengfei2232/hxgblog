<?php

//线上，线下开关
define('IS_ONLINE', false);
/**
 * 请求后端资源的接口=======>URL常量配置
 */
//下载根目录
define('RESOURCE_ROUTE_DIR' , 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

//线下：请求后端接口基础URL
define('DEV_BACKEND_URL', 'http://localhost:80/');

//线上：请求后端接口基础URL
define('ONLINE_BACKEND_URL', 'https://blogback.zhangtengfei-steven.cn/');

$BACKEND_URL = DEV_BACKEND_URL;

if (IS_ONLINE) {
    $BACKEND_URL = ONLINE_BACKEND_URL;
}

//真实的后端请求URL
define('BACKEND_URL', $BACKEND_URL);

//真实请求前端页面基础URL
define('FRONT_END_URL', 'https://hxgblog.zhangtengfei-steven.cn/');

//获取资源接口名字
define('RESOURCE_ROUTE_NAME', 'getResource');

//下载接口名字
define('DOWNLOAD_FILE_ROUTE_NAME', 'downloadFile');

//磁盘名
define('DISK_NAME', 'disk');

//获取资源基础URL
define('RESOURCE_BASE_URL', BACKEND_URL . RESOURCE_ROUTE_NAME . '?' . DISK_NAME . '=');

//下载资源基础URL
define('DOWNLOAD_FILE_BASE_URL', BACKEND_URL . DOWNLOAD_FILE_ROUTE_NAME . DISK_NAME . '=');

//头像文件夹名
define('HEAD_PORTRAIT_FOLDER_NAME', 'head_portrait');

//音乐文件夹名
define('MUSIC_FOLDER_NAME', 'music');

//音乐歌词文件夹名
define('MUSIC_LYRIC_FOLDER_NAME', 'music_lyric');

//文章图片文件夹名
define('ARTICLE_PHOTO_FOLDER_NAME', 'article_photo');

//文章封面图片文件夹名
define('ARTICLE_COVER_FOLDER_NAME', 'article_cover');

//相册图片文件夹名
define('ALBUM_PHOTO_FOLDER_NAME', 'album');


/**
 * 数据库设置的资源字段名配置
 */

//头像路径字段名
define('HEAD_PORTRAIT_FIELD_NAME', 'head_portrait');

//音乐路径字段名
define('MUSIC_FIELD_NAME', 'exh_name');

//音乐歌词路径字段名
define('MUSIC_LYRIC_FIELD_NAME', 'exh_content');

//文章图片路径字段名
define('ARTICLE_PHOTO_FIELD_NAME', 'article_photo');

//文章封面图片路径字段名
define('ARTICLE_COVER_FIELD_NAME', 'art_cover');

//相册图片路径字段名
define('ALBUM_PHOTO_FIELD_NAME', 'pho_path');


/**
 * URL
 */

//文件名参数字段名
define('FILE_NAME', 'filename');

//请求头像的URL
define('HEAD_PORTRAIT_URL', RESOURCE_BASE_URL . HEAD_PORTRAIT_FOLDER_NAME . '&' . FILE_NAME . '=');

//请求文章封面的URL
define('ARTICLE_COVER_URL', RESOURCE_BASE_URL . ARTICLE_COVER_FOLDER_NAME . '&' . FILE_NAME . '=');

//请求文章图片的URL
define('ARTICLE_PHOTO_URL', RESOURCE_BASE_URL . ARTICLE_PHOTO_FOLDER_NAME . '&' . FILE_NAME . '=');

//请求音乐的URL
define('MUSIC_URL', RESOURCE_BASE_URL . MUSIC_FOLDER_NAME . '&' . FILE_NAME . '=');

//请求音乐歌词的URL
define('MUSIC_LYRIC_URL', RESOURCE_BASE_URL . MUSIC_LYRIC_FOLDER_NAME . '&' . FILE_NAME . '=');

//请求相册图片的URL
define('ALBUM_PHOTO_URL', RESOURCE_BASE_URL . ALBUM_PHOTO_FOLDER_NAME . '&' . FILE_NAME . '=');



//下载音乐资源URL
define('download_music', DOWNLOAD_FILE_BASE_URL . MUSIC_FOLDER_NAME . '&' . FILE_NAME . '=');



/**
 * 请求第三方登录接口======>URL常量配置
 */


/**
 * 百度账号登录
 */
//百度基础配置
$bai_du_login_cg = config('bai_du');

//scope
define('BAI_DU_SCOPE', 'netdisk');

//百度第三方登录基础URL
define('BAI_DU_BASE_LOGIN_URL', 'http://openapi.baidu.com/oauth/2.0/authorize?response_type=code&scope=' . $bai_du_login_cg['scope']);

//百度回调接口名
define('BAI_UD_LOGIN_ROUTE_NAME', 'baiDu');

//百度第三方回调URL
define('BAI_DU_REDIRECT_URI', BACKEND_URL . BAI_UD_LOGIN_ROUTE_NAME);

//请求百度参数
define('BAI_DU_PARAM', 'client_id=' . $bai_du_login_cg['client_id'] . '&redirect_uri=' . BAI_DU_REDIRECT_URI);

//百度账号登录URL
define('BAI_DU_LOGIN_URL', BAI_DU_BASE_LOGIN_URL . BAI_DU_PARAM);

//百度头像基础URL
define('BAI_DU_HEAD_PORTRAIT_BASE_URL', 'http://tb.himg.baidu.com/sys/portraitn/item/');


/**
 * QQ登录
 */
//QQ基础配置
$qq_login_cg = config('qq');

//QQ第三方登录基础URL
define('QQ_BASE_LOGIN_URL', 'https://graph.qq.com/oauth2.0/show?which=Login&display=pc&response_type=code');

//QQ回调接口名
define('QQ_LOGIN_ROUTE_NAME', 'qq');

//QQ第三方回调URL
define('QQ_REDIRECT_URI', BACKEND_URL . QQ_LOGIN_ROUTE_NAME);

//请求QQ参数
define('QQ_PARAM', 'client_id=' . $qq_login_cg['client_id'] . '&redirect_uri=' . QQ_REDIRECT_URI);

//QQ登录URL
define('QQ_LOGIN_URL', QQ_BASE_LOGIN_URL . QQ_PARAM);


/**
 * 支付宝登录
 */
//支付宝基础配置
$ali_pay_login_cg = config('ali_pay')['login'];

//支付宝第三方登录基础URL
define('ALI_PAY_BASE_LOGIN_URL', 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?scope=' . $ali_pay_login_cg['scope']);

//支付宝回调接口名
define('ALI_PAY_LOGIN_ROUTE_NAME', 'aliPayLoginCallBack');

//支付宝第三方回调URL
define('ALI_PAY_REDIRECT_URI', BACKEND_URL . ALI_PAY_LOGIN_ROUTE_NAME);

//请求支付宝参数
define('ALI_PAY_PARAM', 'client_id=' . $ali_pay_login_cg['client_id'] . '&redirect_uri=' . ALI_PAY_REDIRECT_URI);

//支付宝登录URL
define('ALI_PAY_LOGIN_URL', ALI_PAY_BASE_LOGIN_URL . ALI_PAY_PARAM);


/**
 * 微博登录
 */
//微博基础配置
$wei_bo_login_cg = config('wei_bo')['login'];

//微博第三方登录基础URL
define('WEI_BO_BASE_LOGIN_URL', 'https://api.weibo.com/oauth2/authorize?response_type=code');

//微博回调接口名
define('WEI_BO_LOGIN_ROUTE_NAME', 'weiBoOAuth');

//微博第三方回调URL
define('WEI_BO_REDIRECT_URI', BACKEND_URL . WEI_BO_LOGIN_ROUTE_NAME);

//请求微博参数
define('WEI_BO_PARAM', 'client_id=' . $wei_bo_login_cg['client_id'] . '&redirect_uri=' . WEI_BO_REDIRECT_URI);

//微博登录URL
define('WEI_BO_LOGIN_URL', WEI_BO_BASE_LOGIN_URL . WEI_BO_PARAM);


/**
 * github登录
 */
//github基础配置
$github_login_cg = config('github');

//github第三方登录基础URL
define('GITHUB_BASE_LOGIN_URL', 'https://github.com/login/oauth/authorize?response_type=code&scope=user');

//github回调接口名
define('GITHUB_LOGIN_ROUTE_NAME', 'gitHub');

//github第三方回调URL
define('GITHUB_REDIRECT_URI', BACKEND_URL . GITHUB_LOGIN_ROUTE_NAME);

//请求QQ参数
define('GITHUB_PARAM', 'client_id=' . $github_login_cg['client_id'] . '&redirect_uri=' . GITHUB_REDIRECT_URI);

//QQ登录URL
define('GITHUB_LOGIN_URL', GITHUB_BASE_LOGIN_URL . GITHUB_PARAM);








