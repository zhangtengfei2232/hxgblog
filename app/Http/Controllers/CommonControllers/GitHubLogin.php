<?php


namespace App\Http\Controllers\CommonControllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubLogin extends Controller
{
    public function gitHubCallBack(Request $request)
    {
        if ($request->has('code')) {
            Log::info('xxxxxx:' . json_encode($request->code));
            $github_login_cg = config('github');
            $param = array(
                'code' => $request->code,
                'client_id' => $github_login_cg['client_id'],
                'client_secret' => $github_login_cg['client_secret'],
            );
            $url = $github_login_cg['access_token_url'];
            $content = getHttpResponsePOST($url, $param);         //获取access_token
            Log::info($content);
            $data = array();
            parse_str($content,$data);
            //请求用户信息
            if (!empty($data['access_token'])) {
                $info_url = $github_login_cg['get_user_url'] . $data['access_token'];
                $token = $data['access_token'];
                $headers[] = 'Authorization: token '. $token;
                $headers[] = "User-Agent: 坏小哥博客";
                $result = getHttpResponseGET($info_url, $headers);
                $info = json_decode($result, true);
                Log::info($info);
            }
        }
    }





}