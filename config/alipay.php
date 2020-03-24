<?php

return [
    'login' => [
        'app_id' => '2019041463863852',
        'method' => 'alipay.system.oauth.token',
        'format' => 'json',
        'charset' => 'utf-8',
        'sign_type' => 'RSA2',
        'version' => '1.0',
        'scope' => 'auth_user',
        'grant_type' => 'authorization_code',
        'base_url' => 'https://openapi.alipay.com/gateway.do',
        'get_token_api' => 'alipay.system.oauth.token',
        'user_info_api' => 'alipay.user.info.share'
    ]
];
