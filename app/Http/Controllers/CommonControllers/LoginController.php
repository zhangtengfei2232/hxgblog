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
            $user = $user->toArray();
            return responseToJson(0,'登录成功',$user);
        }
        return responseToJson(2,'登录失败');
    }

    /**
     * 后台用户登录
     */
    public function backLogin(Request $request)
    {
        if($this->attemptLogin($request)){
            $user = $this->guard()->user();
            $user->generateToken();
            $this->loginSuccess($user, 2);
            $user = $user->toArray();
            return responseToJson(0,'登录成功',$user);
        }
        return responseToJson(2,'登录失败');
    }

    /**
     * 前台用户退出
     * @return \Illuminate\Http\JsonResponse
     */
    public function frontLogout()
    {
        $user = Auth::guard('api')->user();     //用户退出，token清空
        if($user){
            if(empty(session('admin'))){          //后台管理员没有登录,直接清空api_token
                $user->api_token = null;
                $user->save();
            }
        }
        session()->forget('user');
        return responseToJson(0,'退出成功');
    }

    /**
     * 后台用户退出
     * @return \Illuminate\Http\JsonResponse
     */
    public function backLogout()
    {
        $user = Auth::guard('api')->user();     //用户退出，token清空
        if($user){
            $user->api_token = null;
            $user->save();
        }
        session()->forget('admin');
        return responseToJson(0,'退出成功');
    }

    /**登录成功之后调用
     * @param $user
     */
    public function loginSuccess($information, $status = 1)
    {
        ($status == 1) ? session(['user' => $information]) : session(['admin' => $information]);
    }

    /**
     * 判断 '前台用户 / 后台管理员' 是否处于登录状态
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLogin(Request $request)
    {
        if($request->status == 1){
            if(empty(session('user'))) return responseToJson(2,'你未登录');
        }else {
            if(empty(session('admin'))) return responseToJson(2,'你未登录');
        }
        return responseToJson(0,'已登录');
    }

    /**
     * 判断后台管理员与前台用户是否同时在线
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserOrAdminLogin(){
        if(empty(session('user')) && empty(session('admin'))){
            return responseToJson(2,'未登录');
        }
        return responseToJson(1,'已登录');
    }


}