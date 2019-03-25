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
    return responseToJson(1,'未认证或认证失败');
})->name('unAuth');
/**
 * 登录、退出路由
 */
Route::get('/login', 'LoginControllers/LoginController@login');

/**
 * 前台入口路由
 */
Route::get('/', function () {
    return view('welcome');
});
Route::namespace('FrontControllers')->group(function () {
    Route::get('mainPage', 'MainpageController@showMainPage');                   //显示主页面
    Route::get('showArticalPage', 'ArticalController@showArticalPage');          //显示文章页面
    Route::get('byTypeSelectArtical', 'ArticalController@byTypeSelectArtical');  //根据文章类型搜索文章
    Route::get('showArticalDetail', 'ArticalController@showArticalDetail');      //查一篇文章的所有内容
    Route::post('byNameSelectArtical', 'ArticalController@byNameSelectArtical');  //根据文章名字模糊查询文章

    //前台需要验证的路由
    Route::middleware('auth:api')->group(function () {

    });
});
Route::namespace('LoginControllers')->group(function () {
    //用户退出
    Route::middleware('auth:api')->group(function () {
        //前台用户退出
        Route::get('/logout', 'LoginControllers/LoginController@logout');
    });
});
//Route::any('test','Test@showArtical');
/**
 * 后台入口路由
 */
Route::get('/admin', function () {
    return view('admin');
});