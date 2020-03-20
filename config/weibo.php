<?php

return [
    'login' => [
        'grant_type' => 'authorization_code',                            //获取code传递字段
        'client_id' => '131404957',
        'client_secret' => '1e3777f531d0447eed3d6419a934c7f9',
        'oauth_redirect_uri' => 'https://blogback.zhangtengfei-steven.cn/weiBoOAuth',
        'cancel_oauth_redirect_uri' => 'https://blogback.zhangtengfei-steven.cn/weiBoCancelOAuth',
        'access_token_url' => 'https://api.weibo.com/oauth2/access_token?',  //获取token地址
        'user_info_url' => 'https://api.weibo.com/2/users/show.json?'        //获取信息地址
    ],

];