<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

class SessionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    /**
     * 显示登录页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     *  用户登录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' =>'required',
        ]);

        //使用email字段在数据库中查找，并对比password的结果
        //记住我功能 attempt第二个参数，
        if(Auth::attempt($credentials,$request->has('remember'))){
            session()->flash('success',"登录成功");
            return redirect()->intended(route('users.show',[Auth::user()]));
        } else {
            session()->flash('danger',"您的邮箱或用户名不匹配");
            return redirect()->back();
        }
        return;
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success',"您已成功退出");
        return redirect('login');
    }
}
