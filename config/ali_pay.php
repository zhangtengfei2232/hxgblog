<?php

return [
    'login' => [
        'app_id'        => '2019041463863852',                     //应用ID
        'format'        => 'json',                                 //要求支付宝返回的数据格式
        'charset'       => 'utf-8',                                //我们的请求参数的编码格式
        'sign_type'     => 'RSA2',                                 //签名加密方式
        'version'       => '1.0',                                  //调用接口的版本
        'scope'         => 'auth_user',                            //获取的信息类型
        'grant_type'    => 'authorization_code',                   //获取code需要传，固定格式
        'base_url'      => 'https://openapi.alipay.com/gateway.do',//请求支付宝的基础接口地址
        'get_token_api' => 'alipay.system.oauth.token',            //请求access_token的接口名
        'user_info_api' => 'alipay.user.info.share'                //请求用户信息的接口名
    ]
];
