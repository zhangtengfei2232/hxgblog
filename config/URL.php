<?php

/**
 * 请求后端资源的接口=======>URL常量配置
 */
//下载根目录
define('DOWNLOAD_ROUTE_DIR' , 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

//请求后端接口
define('BACK_END_URL', 'https://blogback.zhangtengfei-steven.cn/');

//图片接口名字
define('PHOTO_ROUTE_NAME', 'getPhoto');

//下载接口名字
define('DOWNLOAD_FILE_ROUTE_NAME', 'downloadFile');

//磁盘名
define('DISK_NAME', 'disk');

//获取图片基础URL
define('PHOTO_BASE_URL', BACK_END_URL . PHOTO_ROUTE_NAME . '?' . DISK_NAME . '=');

//下载资源基础URL
define('DOWNLOAD_FILE_BASE_URL', BACK_END_URL . DOWNLOAD_FILE_ROUTE_NAME . DISK_NAME . '=');

//图片文件夹名
define('HEAD_PORTRAIT_FOLDER_NAME', 'head_portrait');

//音乐文件夹名
define('MUSIC_FOLDER_NAME', 'music');

//文章图片文件夹名
define('ARTICLE_PHOTO_FOLDER_NAME', 'article_photo');

//文章封面图片文件夹名
define('ARTICLE_COVER_FOLDER_NAME', 'article_cover');

//相册图片文件夹名
define('ALBUM_FOLDER_NAME', 'album');


//文件名参数字段名
define('FILE_NAME', 'filename');

//请求头像的URL
define('HEAD_PORTRAIT_URL', PHOTO_BASE_URL . HEAD_PORTRAIT_FOLDER_NAME . '&' . FILE_NAME . '=');

//请求文章封面的URL
define('ARTICLE_COVER_URL', PHOTO_BASE_URL . ARTICLE_COVER_FOLDER_NAME . '&' . FILE_NAME . '=');

//请求文章图片的URL
define('ARTICLE_PHOTO_URL', PHOTO_BASE_URL . ARTICLE_PHOTO_FOLDER_NAME . '&' . FILE_NAME . '=');

//请求相册图片的URL
define('ALBUM_PHOTO_URL', PHOTO_BASE_URL . ALBUM_FOLDER_NAME . '&' . FILE_NAME . '=');

//下载音乐资源URL
define('download_music', DOWNLOAD_FILE_BASE_URL . MUSIC_FOLDER_NAME . '&' . FILE_NAME . '=');



/**
 * 请求第三方登录接口======>URL常量配置
 */


/**
 * 百度账号登录
 */
//百度基础配置
$bai_du_login_cg = config('baidu');

//scope
define('BAI_DU_SCOPE', 'netdisk');

//百度第三方登录基础URL
define('BAI_DU_BASE_LOGIN_URL', 'http://openapi.baidu.com/oauth/2.0/authorize?response_type=code&scope=' . $bai_du_login_cg['scope']);

//百度回调接口名
define('BAI_UD_LOGIN_ROUTE_NAME', 'baiDu');

//百度第三方回调URL
define('BAI_DU_REDIRECT_URI', BACK_END_URL . BAI_UD_LOGIN_ROUTE_NAME);

//请求百度参数
define('BAI_DU_PARAM', 'client_id=' . $bai_du_login_cg['client_id'] . '&redirect_uri=' . BAI_DU_REDIRECT_URI);

//百度账号登录URL
define('BAI_DU_LOGIN_URL', BAI_DU_BASE_LOGIN_URL . BAI_DU_PARAM);


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
define('QQ_REDIRECT_URI', BACK_END_URL . QQ_LOGIN_ROUTE_NAME);

//请求QQ参数
define('QQ_PARAM', 'client_id=' . $qq_login_cg['client_id'] . '&redirect_uri=' . QQ_REDIRECT_URI);

//QQ登录URL
define('QQ_LOGIN_URL', QQ_BASE_LOGIN_URL . QQ_PARAM);


/**
 * 支付宝登录
 */
//支付宝基础配置
$ali_pay_login_cg = config('alipay')['login'];

//支付宝第三方登录基础URL
define('ALI_PAY_BASE_LOGIN_URL', 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?scope=' . $ali_pay_login_cg['scope']);

//支付宝回调接口名
define('ALI_PAY_LOGIN_ROUTE_NAME', 'aliPayLoginCallBack');

//支付宝第三方回调URL
define('ALI_PAY_REDIRECT_URI', BACK_END_URL . ALI_PAY_LOGIN_ROUTE_NAME);

//请求支付宝参数
define('ALI_PAY_PARAM', 'client_id=' . $ali_pay_login_cg['client_id'] . '&redirect_uri=' . ALI_PAY_REDIRECT_URI);

//支付宝登录URL
define('ALI_PAY_LOGIN_URL', ALI_PAY_BASE_LOGIN_URL . ALI_PAY_PARAM);


/**
 * 微博登录
 */
//微博基础配置
$wei_bo_login_cg = config('weibo')['login'];

//微博第三方登录基础URL
define('WEI_BO_BASE_LOGIN_URL', 'https://api.weibo.com/oauth2/authorize?response_type=code');

//微博回调接口名
define('WEI_BO_LOGIN_ROUTE_NAME', 'weiBoOAuth');

//微博第三方回调URL
define('WEI_BO_REDIRECT_URI', BACK_END_URL . WEI_BO_LOGIN_ROUTE_NAME);

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
define('GITHUB_REDIRECT_URI', BACK_END_URL . GITHUB_LOGIN_ROUTE_NAME);

//请求QQ参数
define('GITHUB_PARAM', 'client_id=' . $github_login_cg['client_id'] . '&redirect_uri=' . GITHUB_REDIRECT_URI);

//QQ登录URL
define('GITHUB_LOGIN_URL', GITHUB_BASE_LOGIN_URL . GITHUB_PARAM);




