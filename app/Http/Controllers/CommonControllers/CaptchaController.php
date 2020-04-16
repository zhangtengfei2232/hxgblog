<?php


namespace App\Http\Controllers\CommonControllers;


use App\Http\Controllers\Controller;


class CaptchaController extends Controller
{
    /**
     * 生成验证码
     * @return string
     */
    public function getCaptcha()
    {
        return \captcha_src();
    }



}
