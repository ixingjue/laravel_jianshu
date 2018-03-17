<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function welcome()
    {
        return redirect("/login");
    }

    //登录页面
    public function index()
    {
        if (\Auth::check()) {
            return redirect('/posts');
        }
        return view('login/index');
    }

    //登录行为
    public function login()
    {
        //验证
        $this->validate(request(), [
            'email' => 'required|email',
            'password' => 'required|min:5|max:10',
        ]);
        //逻辑
        $user = request(['email', 'password']);
        if (\Auth::attempt($user)) {
            return redirect('/index');
        }
        //渲染
        return \Redirect::back()->withErrors("邮箱密码不匹配");
    }

    //登出行为
    public function logout()
    {
        \Auth::logout();
        return redirect('/login');
    }
}
