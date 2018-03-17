<?php

namespace App\Admin\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
class LoginController extends Controller
{

    //登陆展示页
    public function index()
    {
        if (\Auth::guard("admin")->check()) {
            return redirect()->route('admin.home');
        }
        return view('admin/login/index');
    }

    //登录操作
    public function login()
    {
        //验证
        $this->validate(request(), [
            'name' => 'required|min:2',
            'password' => 'required|min:5|max:10',
        ]);
        //逻辑
        $user = request(['name', 'password']);
        if (\Auth::guard("admin")->attempt($user)) {
            return redirect('/admin/home');
        }
        //渲染
//        dd(\Auth::attempt($user));
        return \Redirect::back()->withErrors("用户名密码不匹配！");
    }
    //登出行为
    public function logout()
    {
        \Auth::guard("admin")->logout();
        return redirect('/admin/login');
    }
}