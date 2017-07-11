<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Symfony\Component\HttpFoundation\Request;


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

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:"^1[0-9]{10}$"|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'department' => 'required|integer',
        ],[
            'phone.regex' => '手机格式不正确！',
            'phone.required' => '手机号必须填写！',
            'phone.unique' => '手机账户已存在！',
            'password.min' => '密码最少要6位数！',
            'password.confirmed' => '两次密码不一样！',
        ],[]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
//            'email' => $data['email'],
            'phone' => $data['phone'],
            'department' =>$data['department'],
            'password' => bcrypt($data['password']),
        ]);
    }

    //注册显示页面
    public function showRegistrationForm(Request $request)
    {
        $title1 = '首页';
        $title2 = '注册';
        $titleLink1 = '/';
        $titleLink2 = '/register';

        //按发送短信后保留已写内容
        if($request->session()->has('create_user_phone')){
            $phone = $request->session()->get('create_user_phone');
            $request->session()->forget('create_user_phone');
        }else{
            $phone = null;
        }
        if($request->session()->has('create_user_name')){
            $name = $request->session()->get('create_user_name');
            $request->session()->forget('create_user_name');
        }else{
            $name = null;
        }
        if($request->session()->has('create_user_password')){
            $password = $request->session()->get('create_user_password');
            $request->session()->forget('create_user_password');
        }else{
            $password = null;
        }
        if($request->session()->has('create_user_password_confirm')){
            $passwordConfirm = $request->session()->get('create_user_password_confirm');
            $request->session()->forget('create_user_password_confirm');
        }else{
            $passwordConfirm = null;
        }

        //发送短信是 没填电话号码
        if($request->session()->has('send_message_error')){
            $sendMessageError = $request->session()->get('send_message_error');
            $request->session()->forget('send_message_error');
        }else{
            $sendMessageError = null;
        }

        //发送短信的手机号和注册的不一样
        if($request->session()->has('phone_changed')){
            $phoneChanged = $request->session()->get('phone_changed');
            $request->session()->forget('phone_changed');
        }else{
            $phoneChanged = null;
        }

        //验证码输入错误
        if($request->session()->has('authenticationCodeError')){
            $authenticationCodeError = $request->session()->get('authenticationCodeError');
            $request->session()->forget('authenticationCodeError');
        }else{
            $authenticationCodeError = null;
        }

        if($request->session()->has('isClickSendMessage')){
            $isClickSendMessage = $request->session()->get('isClickSendMessage');
            $request->session()->forget('isClickSendMessage');
        }else{
            $isClickSendMessage = false;
        }

        return view('auth.register', compact('title1','titleLink1', 'titleLink2','title2', 'phone', 'name', 'password', 'passwordConfirm', 'sendMessageError', 'phoneChanged', 'authenticationCodeError', 'isClickSendMessage'));
    }
    
    //发生短信验证吗
    public function sendMessage(Request $request){
        $validator = \Validator::make($request->all(), [
            'phone' => 'required|regex:"^1[0-9]{10}$"|unique:users'
        ],['phone.regex' => '手机格式不正确！',
            'phone.required' => '手机号必须填写！',
            'phone.unique' => '手机账户已存在！',
        ],[]);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
        }
        if($request->session()->has('create_user_phone')){
            $request->session()->forget('create_user_phone');
            $request->session()->put('create_user_phone',$request->get('phone'));
            $request->session()->put('send_message_phone',$request->get('phone'));
        }else{
            $request->session()->put('create_user_phone',$request->get('phone'));
            $request->session()->put('send_message_phone',$request->get('phone'));
        }
        if($request->session()->has('create_user_name')){
            $request->session()->forget('create_user_name');
            $request->session()->put('create_user_name',$request->get('name'));
        }else{
            $request->session()->put('create_user_name',$request->get('name'));
        }
        if($request->session()->has('create_user_password')){
            $request->session()->forget('create_user_password');
            $request->session()->put('create_user_password',$request->get('password'));
        }else{
            $request->session()->put('create_user_password',$request->get('password'));
        }
        if($request->session()->has('create_user_password_confirm')){
            $request->session()->forget('create_user_password_confirm');
            $request->session()->put('create_user_password_confirm',$request->get('password-confirm'));
        }else{
            $request->session()->put('create_user_password_confirm',$request->get('password-confirm'));
        }

        if(!$request->has('phone')){
            if($request->session()->has('send_message_error')){
                $request->session()->forget('send_message_error');
                $request->session()->put('send_message_error','您没有填要发到的手机号码!');
            }
            $request->session()->put('send_message_error','您没有填要发到的手机号码!');
        }else{
            if($request->session()->has('isClickSendMessage')){
                $request->session()->forget('isClickSendMessage');
                $request->session()->put('isClickSendMessage', true);
            }else{
                $request->session()->put('isClickSendMessage', true);
            }
        }

        $createCode = rand(100000, 999999);
        if($request->session()->has('createCode')){
            $request->session()->forget('createCode');
            $request->session()->put('createCode', $createCode);
        }else{
            $request->session()->put('createCode', $createCode);
        }

        $client = \Hprose\Client::create(config('app.HPROSE_IP'), false);
        $content = '您收到了创建kpi考勤系统的验证码，将其输入到验证码输入框。验证码为：'.$createCode;
        $client->push_sms_store($request->get('phone'),$content);

        return redirect()->action('Auth\RegisterController@showRegistrationForm');
    }

    //注册
    public function register(Request $request)
    {
        if($request->session()->get('send_message_phone') != $request->get('phone')){
            $request->session()->put('phone_changed', '您发送的验证码手机号码和要注册的不同！');
            return redirect()->back()->withInput($request->all());
        }

        if($request->get('authenticationCode') == ($request->session()->has('createCode') ? $request->session()->get('createCode') : null))
        {}else{
            $request->session()->put('authenticationCodeError', '您填写的验证码不正确！');
            return redirect()->back()->withInput($request->all());
        }

        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
}
