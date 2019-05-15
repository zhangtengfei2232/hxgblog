<?php
namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    /**
     * 登录使用的账号字段名
     * @return string
     */
    public function username()
    {
        return 'phone';
    }

    /**
     * 认证服务方（登录时需要使用SessionGuard, 访问api时使用TokenGuard）
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard('web');              //session
    }

    /**
     * 前台用户登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function frontLogin(Request $request)
    {
        if($this->attemptLogin($request)){
            $user = $this->guard()->user();
            $user->generateToken();
            $this->loginSuccess($user);
            return responseToJson(0,'登录成功',$user->toArray());
        }
        return responseToJson(2,'登录失败');
    }

    /**
     * 后台用户登录
     */
    public function backLogin()
    {

    }

    /**
     * 用户退出
     * @return \Illuminate\Http\JsonResponse
     */
    public function frontLogout()
    {
        $user = Auth::guard('api')->user();     //用户退出，token清空
        if($user){
            $user->api_token = null;
            $user->save();
        }
        return responseToJson(0,'退出成功');
    }

    /**登录成功之后调用
     * @param $user
     */
    public function loginSuccess($user)
    {
        session(['user' => $user]);
    }

    /**
     * 判断用户是否处于登录状态
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLogin()
    {
        if(empty(session('user'))) return responseToJson(1,'你未登录');
        return responseToJson(0,'已登录');
    }


}