<?php

namespace App\Http\Controllers\Auth;

use App\Investor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Tools\CacheDrive;

use Session;
use Cache;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    use CacheDrive;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected function redirectTo()
    {
        return '/';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('cache');
    }

    public function showRegistrationForm(){
        return view('auth.register')->withTitle(' | 注册');
    }   


    public function register(Request $request){
        
        $v_data = $request->all();
        $validator = $this->validator($v_data);


        if ($validator->fails()){
            $errors = implode(', ', $validator->errors()->all());
            Sesssion::flash('failure', $errors);
            return redirect('auth.register')->withTitle(' | 注册');
        }

        $investor = new Investor;

        $investor->name = $request->name;
        $investor->email = $request->email;
        $investor->password = bcrypt($request->password);
       
        $config = $this->getGlobalConfig();

        if (!empty($config) && isset($config['init_credits']))             
             $investor->coins = intval($config['init_credits']);
        else
            $investor->coins = 10000;

        $investor->level = 1; 

        if (!empty($config) && isset($config['default_init_currency']))             
             $investor->bind_to = $config['default_init_currency'];
        else
            $investor->bind_to = 'cny';

        $investor->save();

        Auth::login($investor, true);

        Session::flash('success', '注册成功');
        return redirect('/')->withTitle('');
    }
           
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|min:5|max:255|unique:investors',
            'password' => 'required|string|min:6|confirmed',
            'email' => 'required|string|email|max:255',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return Investor::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'coins' => 10000, //changable
            'level' => 1,
        ]);
    }
}
