<?php

return [
    'grant_type'       => 'authorization_code',                                                     //获取code传递字段
    'client_id'        => 'YDw7v3c1HesiCqhBBZuHpqlI',
    'client_secret'    => '3eCGPhayY05N7M7UZqF5vVmFFiRUf3Fb',
    'redirect_uri'     => 'https://blogback.zhangtengfei-steven.cn/baiDu',
    'access_token_url' => 'https://openapi.baidu.com/oauth/2.0/token',                              //获取token地址
    'user_info_url'    => 'https://openapi.baidu.com/rest/2.0/passport/users/getInfo?access_token=',//获取信息地址
    'scope'            => 'netdisk'                                                                 //授权，要获取资源的标识
];
