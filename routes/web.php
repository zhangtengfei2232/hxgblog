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
 * 登录、退出路由
 */
Route::get('/login', 'LoginControllers/LoginController@login');
Route::get('/logout', 'LoginControllers/LoginController@logout');
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

});
//Route::any('test','Test@showArtical');
/**
 * 后台入口路由
 */
Route::get('/admin', function () {
    return view('admin');
});