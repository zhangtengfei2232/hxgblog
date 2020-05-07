<?php

//线上，线下开关
define('IS_ONLINE', true);
/**
 * 请求后端资源的接口=======>URL常量配置
 */
//下载根目录
define('RESOURCE_ROUTE_DIR' , 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

//线下：请求后端接口基础URL
define('DEV_BACKEND_URL', 'http://localhost:88/');

//线上：请求后端接口基础URL
define('ONLINE_BACKEND_URL', 'https://blogback.zhangtengfei-steven.cn/');

//线上请求前端页面基础URL
define('ONLINE_FRONTEND_URL', 'https://hxgblog.zhangtengfei-steven.cn/');

//线上请求前端页面基础URL
define('DEV_FRONTEND_URL', 'http://localhost:8080');

$BACKEND_URL = DEV_BACKEND_URL;

$FRONTEND_URL = DEV_FRONTEND_URL;

if (IS_ONLINE) {
    $BACKEND_URL = ONLINE_BACKEND_URL;
    $FRONTEND_URL = ONLINE_FRONTEND_URL;
}

//真实的后端请求URL
define('BACKEND_URL', $BACKEND_URL);

//真实请求前端页面基础URL
define('FRONTEND_URL', $FRONTEND_URL);

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
define('ALBUM_PHOTO_FOLDER_NAME', 'album_photo');


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
define('ALBUM_PHOTO_FIELD_NAME', 'photo_path');

//相册最新的一张图片字段名
define('ALBUM_FIRST_PHOTO_FIELD_NAME', 'first_photo_path');


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
 * 公共配置
 */
//获取code传递字段标识
define('GRANT_TYPE', 'authorization_code');


/**
 * 百度账号登录
 */
//授权，要获取资源的标识
define('BAI_DU_SCOPE', 'netdisk');

//appID
define('BAI_DU_CLIENT_ID', 'YDw7v3c1HesiCqhBBZuHpqlI');

//app秘钥KEY
define('BAI_DU_CLIENT_SECRET', '3eCGPhayY05N7M7UZqF5vVmFFiRUf3Fb');

//获取token地址
define('BAI_DU_ACCESS_TOKEN_URL', 'https://openapi.baidu.com/oauth/2.0/token');

//获取信息地址
define('BAI_DU_USER_INFO_URL', 'https://openapi.baidu.com/rest/2.0/passport/users/getInfo?access_token=');


//百度第三方登录基础URL
define('BAI_DU_BASE_LOGIN_URL', 'http://openapi.baidu.com/oauth/2.0/authorize?response_type=code&scope=' . BAI_DU_SCOPE);

//百度回调接口名
define('BAI_UD_LOGIN_ROUTE_NAME', 'baiDu');

//百度第三方回调URL
define('BAI_DU_REDIRECT_URI', BACKEND_URL . BAI_UD_LOGIN_ROUTE_NAME);

//请求百度参数
define('BAI_DU_PARAM', '&client_id=' . BAI_DU_CLIENT_ID . '&redirect_uri=' . BAI_DU_REDIRECT_URI);

//百度账号登录URL
define('BAI_DU_LOGIN_URL', BAI_DU_BASE_LOGIN_URL . BAI_DU_PARAM);

//百度头像基础URL
define('BAI_DU_HEAD_PORTRAIT_BASE_URL', 'http://tb.himg.baidu.com/sys/portraitn/item/');


/**
 * QQ登录
 */
//appID
define('QQ_CLIENT_ID', '101849190');

//app秘钥KEY
define('QQ_CLIENT_SECRET', '0821400afaecda6ac386e3ff0586e99e');

//获取token地址
define('QQ_ACCESS_TOKEN_URL', 'https://graph.qq.com/oauth2.0/token?');

//获取openid地址
define('QQ_OPENID_URL', 'https://graph.qq.com/oauth2.0/me?access_token=');

//获取信息地址
define('QQ_USER_INFO_URL', 'https://graph.qq.com/user/get_user_info?');

//QQ第三方登录基础URL
define('QQ_BASE_LOGIN_URL', 'https://graph.qq.com/oauth2.0/show?which=Login&display=pc&response_type=code');

//QQ回调接口名
define('QQ_LOGIN_ROUTE_NAME', 'qq');

//QQ第三方回调URL
define('QQ_REDIRECT_URI', BACKEND_URL . QQ_LOGIN_ROUTE_NAME);

//请求QQ参数
define('QQ_PARAM', '&client_id=' . QQ_CLIENT_ID . '&redirect_uri=' . QQ_REDIRECT_URI);

//QQ登录URL
define('QQ_LOGIN_URL', QQ_BASE_LOGIN_URL . QQ_PARAM);


/**
 * 支付宝登录
 */
//appID
define('ALI_PAY_CLIENT_ID', '2019041463863852');

//要求支付宝返回的数据格式
define('ALI_PAY_FORMAT', 'json');

//我们的请求参数的编码格式
define('ALI_PAY_CHART_SET', 'utf-8');

//签名加密方式
define('ALI_PAY_SIGN_TYPE', 'RSA2');

//请求接口版本
define('ALI_PAY_VERSION', '1.0');

//获取的信息类型
define('ALI_PAY_SCOPE', 'auth_user');

//请求支付宝接口基础URL
define('ALI_PAY_ICE_BASE_URL', 'https://openapi.alipay.com/gateway.do');

//请求access_token的接口名
define('ALI_PAY_TOKEN_API', 'alipay.system.oauth.token');

//请求access_token的接口名
define('ALI_PAY_USER_INFO_API', 'alipay.user.info.share');

//支付宝第三方登录基础URL
define('ALI_PAY_BASE_LOGIN_URL', 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?scope=' . ALI_PAY_SCOPE);

//支付宝回调接口名
define('ALI_PAY_LOGIN_ROUTE_NAME', 'aliPayLoginCallBack');

//支付宝第三方回调URL
define('ALI_PAY_REDIRECT_URI', BACKEND_URL . ALI_PAY_LOGIN_ROUTE_NAME);

//请求支付宝参数
define('ALI_PAY_PARAM', '&client_id=' . ALI_PAY_CLIENT_ID . '&redirect_uri=' . ALI_PAY_REDIRECT_URI);

//支付宝登录URL
define('ALI_PAY_LOGIN_URL', ALI_PAY_BASE_LOGIN_URL . ALI_PAY_PARAM);


/**
 * 微博登录
 */
//appID
define('WEI_BO_CLIENT_ID', '131404957');

//app秘钥KEY
define('WEI_BO_CLIENT_SECRET', '1e3777f531d0447eed3d6419a934c7f9');

//获取token地址
define('WEI_BO_ACCESS_TOKEN_URL', 'https://api.weibo.com/oauth2/access_token?');

//微博第三方登录基础URL
define('WEI_BO_BASE_LOGIN_URL', 'https://api.weibo.com/oauth2/authorize?response_type=code');

//获取信息地址
define('WEI_BO_USER_INFO_URL', 'https://api.weibo.com/2/users/show.json?');

//微博授权回调接口名
define('WEI_BO_LOGIN_ROUTE_NAME', 'weiBoOAuth');

//微博取消授权回调接口名
define('WEI_BO_CANCEL_OAUTH_ROUTE_NAME', 'weiBoCancelOAuth');

//微博第三方回调URL
define('WEI_BO_OAUTH_REDIRECT_URI', BACKEND_URL . WEI_BO_LOGIN_ROUTE_NAME);

//请求微博参数
define('WEI_BO_PARAM', '&client_id=' . WEI_BO_CLIENT_ID . '&redirect_uri=' . WEI_BO_OAUTH_REDIRECT_URI);

//微博登录URL
define('WEI_BO_LOGIN_URL', WEI_BO_BASE_LOGIN_URL . WEI_BO_PARAM);


/**
 * github登录
 */
//appID
define('GITHUB_CLIENT_ID', '02a70b26ae66f828b8f2');

//app秘钥KEY
define('GITHUB_CLIENT_SECRET', '78b205b3d310d74addd7ef8a3195d38db53374a5');

//获取token地址
define('GITHUB_ACCESS_TOKEN_URL', 'https://github.com/login/oauth/access_token');

//获取信息地址
define('GITHUB_USER_INFO_URL', 'https://api.github.com/user?');

//github第三方登录基础URL
define('GITHUB_BASE_LOGIN_URL', 'https://github.com/login/oauth/authorize?response_type=code&scope=user');

//github回调接口名
define('GITHUB_LOGIN_ROUTE_NAME', 'gitHub');

//github第三方回调URL
define('GITHUB_REDIRECT_URI', BACKEND_URL . GITHUB_LOGIN_ROUTE_NAME);

//请求QQ参数
define('GITHUB_PARAM', '&client_id=' . GITHUB_CLIENT_ID . '&redirect_uri=' . GITHUB_REDIRECT_URI);

//QQ登录URL
define('GITHUB_LOGIN_URL', GITHUB_BASE_LOGIN_URL . GITHUB_PARAM);








