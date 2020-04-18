<?php

return [
    'grant_type'       => 'authorization_code',                            //获取code传递字段
    'client_id'        => '101849190',
    'client_secret'    => '0821400afaecda6ac386e3ff0586e99e',
    'redirect_uri'     => 'https://blogback.zhangtengfei-steven.cn/qq',
    'access_token_url' => 'https://graph.qq.com/oauth2.0/token?',          //获取token地址
    'openid_url'       => 'https://graph.qq.com/oauth2.0/me?access_token=',//获取openid地址
    'user_info_url'    => 'https://graph.qq.com/user/get_user_info?'       //获取信息地址
];
