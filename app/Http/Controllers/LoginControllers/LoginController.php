<?php
namespace App\Http\Controllers\LoginControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LoginController extends Controller
{
    protected function guard()
    {
        return Auth::guard('api');
    }
    public function login(Request $request)
    {

    }
    public function logout()
    {

    }


}