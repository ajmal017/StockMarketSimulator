<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Session;
use App\Investor;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $remember = true;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('cache');
    }

    protected function sendLoginResponse(Request $request){
        
            // 设置记住我的时间为60分钟
            $rememberTokenExpireMinutes = 60;

            // 首先获取 记住我 这个 Cookie 的名字, 这个名字一般是随机生成的,
            // 类似 remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d
            $rememberTokenName = Auth::getRecallerName();

            // 再次设置一次这个 Cookie 的过期时间
            Cookie::queue($rememberTokenName, Cookie::get($rememberTokenName), $rememberTokenExpireMinutes);

            // 下面的代码是从 AuthenticatesUsers 中的 sendLoginResponse() 直接复制而来
            $request->session()->regenerate();

            $this->clearLoginAttempts($request);

            return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }    
 
    public function name()
    {
        return 'name';
    }

    public function showLoginForm(){
        return view('auth.login')->withTitle(' | 登录');
    }
        
    public function login(Request $request){
        if (Auth::attempt(['name' => $request->name, 'password' => $request->password])) {
            $user = Auth::user();

            $investor = Investor::find($user->iid);

            $investor->last_ip = $request->ip;
            $investor->last_login_time = date("Y-m-d G:i:s");

            //dd($investor);

            $investor->save();

            Session::put('iid', $user->iid);
            Session::put('username', $user->name);

            Session::flash('success', '登录成功');

            //dd($request);
            return redirect('/')->withTitle('');
        }        
        else{
            Session::flash('failure', '用户名或密码有错误');
            return back()->withTitle(' | 登陆');
            //return back()->withErrors($errors)->withTitle(' | 登录');
        }    
    }


    public function logout(Request $request){
        Auth::logout();
        Session::flash('success', '成功退出'); //not work
        return redirect('/')->withTitle(''); //not work
    }    
        
}
