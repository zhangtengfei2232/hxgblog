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
    Route::get('mainPage', 'MainpageController@showMainPage');
    Route::get('byTypeSelectArtical', 'ArticalController@byTypeSelectArtical');
});
//Route::any('test','Test@showArtical');
/**
 * 后台入口路由
 */
Route::get('/admin', function () {
    return view('admin');
});