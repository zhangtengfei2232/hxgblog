<?php


namespace App\Http\Controllers\ErrorControllers;


use App\Http\Controllers\Controller;

class EmptyController extends Controller
{
    /**
     * 后台报错，给用户返回404页面
     */
    public function showFourView(){
        return view('error');
    }


    /**
     * 用户没有请求到资源报错，返回给用户资源为空页面
     */
    public function showEmptyView(){
        return view('empty');
    }

}
