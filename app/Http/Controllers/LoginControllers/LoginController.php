<?php
namespace App\Http\Controllers\LoginControllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    /**
     * 登录使用的账号字段名
     * @return string
     */
    public function username()
    {
        return 'name';

    }
    /**
     * 认证服务方（登录时需要使用SessionGuard, 访问api时使用TokenGuard）
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard('web');              //session
    }
    //用户登录
    public function login(Request $request)
    {
        if($this->attemptLogin($request)){
            $user = $this->guard()->user();
            $user->generateToken();
            $this->loginSuccess($user);
            return responseToJson(0,'登录成功',$user->toArray());
        }
    }
    //用户退出
    public function logout()
    {
        $user = Auth::guard('api')->user();     //token
        if($user){
            $user->api_token = null;
            $user->save();
        }
        return responseToJson(0,'退出成功');
    }
    //登录成功之后调用
    public function loginSuccess($user)
    {
        session(['user' => $user]);

    }


}