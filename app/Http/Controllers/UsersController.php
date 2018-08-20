<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        //laravel中间件
        //只让登陆用户查看
        $this->middleware('auth',[
           'except' => ['show','store','create','index','confirmEmail']
        ]);

        //只让未登陆用户查看 注册页
        $this->middleware('guest',[
            'only'  => ['create']
        ]);
    }


    public function index()
    {
        //分页
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    /**
     * 注册页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 显示用户信息
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        $statuses = $user->statuses()
                        ->orderBy('created_at','desc')
                        ->paginate(30);
        return view('users.show',compact('user','statuses'));
    }

    /**
     * 注册用户
     * 用户注册成功后自动进行登录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'     => 'required|max:50',
            'email'    => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name'     =>  $request->name,
            'email'    =>  $request->email,
            'password' => bcrypt($request->password),
        ]);

        //Auth::login($user);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success','注册成功,请查阅邮箱进行激活');
        return redirect('/');
    }

    /**
     * 编辑用户页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        //授权策略  第一个参数是策略名， 第二个为验证的数据
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }


    /**
     * 更新用户信息
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(User $user, Request $request)
    {
        $this->validate($request,[
            'name'     => 'required|max:50',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $this->authorize('update',$user);

        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success','用户信息更新成功');

        return redirect()->route('users.show',$user->id);
    }


    /**
     * 删除用户
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user)
    {
        //授权策略，允许已登录的管理员进行删除
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','删除成功');
        return back();
    }


    /**
     * 发送邮件
     * @param $user
     */
    public function sendEmailConfirmationTo($user)
    {
        $view = "emails.confirm";
        $data = compact('user');
        $to   = $user->email;
        $subject = "感谢注册";
        Mail::send($view, $data, function($message) use ($to, $subject){
            $message->to($to)->subject($subject);
        });

    }

    /**
     * 注册用户邮件确认
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        Auth::login($user);
        session()->flash('success','恭喜注册成功');
        return redirect()->route('users.show',[$user]);
    }


    /**
     * 显示关注人列表
     */
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = "关注的人";
        return view('users.show_follow',compact('users','title'));
    }


    /**
     * 显示粉丝列表
     */
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = "粉丝";
        return view('users.show_follow',compact('users','title'));
    }



}
