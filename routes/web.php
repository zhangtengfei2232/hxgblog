<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/**
 * 用户认证失败的路由
 */
Route::any('unAuth', function () {
    return responseToJson(3,'未认证或认证失败');
})->name('unAuth');


/**
 * 前台入口路由
 */
Route::get('/', function () {
    return view('welcome');
});

/**
 * 错误路由
 */
Route::namespace('ErrorControllers')->group(function () {
   Route::get('showFourView', 'EmptyController@showFourView');
   Route::get('showEmptyView', 'EmptyController@showEmptyView');
});

Route::namespace('FrontControllers')->group(function () {


    Route::get('mainPage', 'MainPageController@showMainPage');                            //显示主页面
    Route::get('showArticlePage', 'ArticleController@showArticlePage');                   //显示文章页面
    Route::get('typeSelectArticle', 'ArticleController@typeSelectArticle');               //根据文章类型搜索文章
    Route::get('showArticleDetail', 'ArticleController@showArticleDetail');               //查一篇文章的所有内容
    Route::post('byNameSelectArticle', 'ArticleController@byNameSelectArticle');          //根据文章名字模糊查询文章
    Route::get('byTopIdSelectAllComment','ArticleController@byTopIdSelectAllComment');    //查某一个评论的所有评论
    Route::get('getArticleAllType', 'ArticleController@getArticleAllType');               //获取文章类型

    Route::get('selectAllAlbumInformation', 'AlbumController@selectAllAlbumInformation'); //查询所有相册信息
    Route::post('judgeQuestionAnswer', 'AlbumController@judgeQuestionAnswer');            //判断相册问题答案是否正确
    Route::get('byAlbumIdSelectPhoto', 'AlbumController@byAlbumIdSelectPhoto');           //根据相册的ID查询照片

    Route::get('selectLeaveMessage', 'LeaveMessageController@selectLeaveMessage');        //查询留言信息
    //前台需要验证的路由
    Route::middleware('loginCheck', 'auth:api', 'updateToken:api')->group(function () {
        Route::post('sendReplayComment','ArticleController@sendReplayComment');            //添加回复评论
        Route::post('addPublishComment', 'ArticleController@addPublishComment');           //添加评论
        Route::post('deleteArticleComment', 'ArticleController@deleteArticleComment');     //删除文章评论
        Route::post('praiseOrTrampleArticle', 'ArticleController@praiseOrTrampleArticle'); //文章赞/踩

        Route::post('replayMessage', 'LeaveMessageController@replayMessage');           //回复留言
        Route::post('deleteLeaveMessage', 'LeaveMessageController@deleteLeaveMessage'); //删除留言
        Route::post('addLeaveMessage', 'LeaveMessageController@addLeaveMessage');       //添加留言

    });
});
//获取资源需要验证
Route::namespace('CommonControllers')->group(function () {

    Route::post('registerUser', 'UserController@registerUser');                       //注册新用户

    /**
     * 登录、退出路由
     */
    // '前台/后台' 用户退出
    Route::middleware('auth:api')->group(function () {

        Route::get('frontLogout', 'LoginController@frontLogout');
        Route::get('backLogout', 'LoginController@backLogout');
    });

    Route::get('downloadFile', 'ObtainFileController@downloadFile');                  //下载后台资源

    Route::get('getCityInfo', 'ObtainFileController@getCityInfo');                    //获取天气城市名

    Route::get('getSmsCode', 'TencentSmsController@getSmsCode');
    Route::post('byCodeUpdatePassword', 'UserController@byCodeUpdatePassword');       //用户根据短信验证码修改密码
    //前台用户登录
    Route::post('frontLogin', 'LoginController@frontLogin');

    //GitHub登录
    //获取code和access_token的github回调地址
    Route::get('gitHub', 'GitHubLogin@gitHubCallBack');

    //百度账号登录
    //获取code和access_token的百度回调地址
    Route::get('baiDu', 'BaiDuLogin@baiDuCallBack');

    //QQ账号登录
    //获取code和access_token的QQ回调地址
    Route::get('qq', 'QQLogin@qqCallBack');

    //微博账号登录
    //获取code和access_token的微博回调地址
    Route::get('weiBoOAuth', 'WeiBoLogin@weiBoOAuthCallBack');
    //取消授权的回调地址
    Route::get('weiBoCancelOAuth', 'WeiBoLogin@weiBoCancelOAuthCallBack');

    //支付宝登录
    Route::get('aliPayLoginCallBack', 'AliPayLogin@aliPayLoginCallBack');

    //前台短信登录
    Route::post('frontSmsLogin', 'LoginController@frontSmsLogin');                    //前台短信登录
    Route::post('backSmsLogin', 'LoginController@backSmsLogin');                      //后台短信登录
    //后台用户登录
    Route::post('backLogin', 'LoginController@backLogin');

    Route::get('getCaptcha', 'CaptchaController@getCaptcha');                         //生成验证码

    //验证前台用户是否登录
    Route::get('checkLogin', 'LoginController@checkLogin');

    //判断后台管理员与前台用户是否同时在线
    Route::get('checkUserOrAdminLogin', 'LoginController@checkUserOrAdminLogin');

    Route::get('getResource', 'ObtainFileController@getResource');
    Route::middleware('loginCheck', 'auth:api', 'updateToken:api')->group(function () {
        Route::get('getUserInformation', 'UserController@getUserInformation');        //获取用户信息
        Route::post('updateUserInformation', 'UserController@updateUserInformation'); //修改用户信息
        Route::post('updatePassword', 'UserController@updatePassword');               //修改用户密码
    });
});
Route::namespace('BackControllers')->group(function (){
    Route::middleware('loginCheck', 'auth:api', 'updateToken:api')->group(function () {

        Route::get('getArticle', 'MaArticleController@getArticle');                        //获取文章
        Route::get('combineSelectArticle', 'MaArticleController@combineSelectArticle');    //组合查询文章
        Route::get('byTypeSelectArticle', 'MaArticleController@byTypeSelectArticle');      //根据文章类型搜索
        Route::get('getAloneArticle', 'MaArticleController@getAloneArticle');              //查询单个文章信息
        Route::post('addArticle', 'MaArticleController@addArticle');                       //添加文章
        Route::post('deleteArticle', 'MaArticleController@deleteArticle');                 //删除文章
        Route::post('updateArticle', 'MaArticleController@updateArticle');                 //修改文章信息
        Route::post('uploadArticlePhoto', 'MaArticleController@uploadArticlePhoto');       //上传文章内容图片
        Route::post('deleteArticlePhoto', 'MaArticleController@deleteArticlePhoto');       //删除文章内容图片

        Route::get('getAlbumInfo', 'MaAlbumController@getAlbumInfo');                           //获取相册信息
        Route::post('addAlbum', 'MaAlbumController@addAlbum');                                  //添加相册信息
        Route::post('deleteAlbum', 'MaAlbumController@deleteAlbum');                            //删除信息
        Route::post('updateAlbumInfo', 'MaAlbumController@updateAlbumInfo');                    //更新相册信息
        Route::post('addAlbumSecretSecurity', 'MaAlbumController@addAlbumSecretSecurity');      //添加相册密保
        Route::post('deleteAlbumSecretSecurity', 'MaAlbumController@deleteAlbumSecretSecurity');//删除相册密保
        Route::post('updateAlbumSecretSecurity', 'MaAlbumController@updateAlbumSecretSecurity');//修改相册密保
        Route::get('selectAlbumPhoto', 'MaAlbumController@selectAlbumPhoto');                   //查询相册照片
        Route::post('addAlbumImage', 'MaAlbumController@addAlbumImage');                        //上传图片
        Route::post('deleteAlbumPhoto', 'MaAlbumController@deleteAlbumPhoto');                  //删除相册图片

        Route::get('selectExhibit', 'MaExhibitController@selectExhibit');                       //查询展览内容
        Route::post('addExhibit', 'MaExhibitController@addExhibit');                            //添加展览内容
        Route::post('deleteExhibit', 'MaExhibitController@deleteExhibit');                      //删除展览内容
        Route::post('updateExhibit', 'MaExhibitController@updateExhibit');                      //修改展览内容
        Route::get('byTimeSelectExhibit', 'MaExhibitController@byTimeSelectExhibit');           //根据时间查询展览内容
        Route::post('replaceExhibit', 'MaExhibitController@replaceExhibit');                    //替换展览内容
        Route::get('selectAloneExhibit', 'MaExhibitController@selectAloneExhibit');             //查单个展览内容

        Route::get('getArtType', 'MaArtTypeController@getArtType');                             //获取文章类型
        Route::get('byTimeSelectArtType', 'MaArtTypeController@byTimeSelectArtType');           //根据时间查询文章类型
        Route::post('addArtType', 'MaArtTypeController@addArtType');                            //添加文章类型
        Route::post('deleteArtType', 'MaArtTypeController@deleteArtType');                      //删除文章类型
        Route::post('updateArtType', 'MaArtTypeController@updateArtType');                      //修改文章类型

    });

});
/**
 * 后台入口路由
 */
Route::get('/admin', function () {
    return view('admin');
});
