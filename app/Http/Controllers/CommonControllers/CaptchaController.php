<?php


namespace App\Http\Controllers\CommonControllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class CaptchaController extends Controller
{
    /**
     * 生成验证码
     * @return string
     */
    public function getCaptcha(Request $request)
    {
        dd(captcha_check($request->ss));
        return \captcha_src();
    }



}