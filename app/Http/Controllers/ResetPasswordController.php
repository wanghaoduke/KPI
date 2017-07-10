<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function show (Request $request){
        $title1 = '首页';
        $title2 = '获取验证码';
        $titleLink1 = '/';
        $titleLink2 = '/reset_get_code';
        //获取没填号码就发验证码的错误信息
        if($request->session()->has('reset_send_code_no_phone')){
            $sendCodeNoPhone = $request->session()->get('reset_send_code_no_phone');
            $request->session()->forget('reset_send_code_no_phone');
        }else{
            $sendCodeNoPhone = null;
        }

        if($request->session()->has('reset_phone_changed')){
            $resetPhoneChanged = $request->session()->get('reset_phone_changed');
            $request->session()->forget('reset_phone_changed');
        }else{
            $resetPhoneChanged = null;
        }

        if($request->session()->has('reset_code_error')){
            $resetCodeError = $request->session()->get('reset_code_error');
            $request->session()->forget('reset_code_error');
        }else{
            $resetCodeError = null;
        }

        if($request->session()->has('isClickSendMessageReset')){
            $isClickSendMessage = $request->session()->get('isClickSendMessageReset');
            $request->session()->forget('isClickSendMessageReset');
        }else{
            $isClickSendMessage = false;
        }
        return view('resetGetCode', compact('title1','title2','titleLink1','titleLink2','sendCodeNoPhone','resetPhoneChanged','resetCodeError','isClickSendMessage'));
    }

    public function sendCode (Request $request){
        \Log::info($request->all());
        $validator = \Validator::make($request->all(), [
            'phone' => 'string|regex:"^1[0-9]{10}$"|exists:users'
        ],['phone.regex' => '手机格式不正确！',
        'phone.exists' => "该手机号码账户不存在！"
        ],[]);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
        }
        if(!$request->has('phone')){
            if($request->session()->has('reset_send_code_no_phone')){
                $request->session()->forget('reset_send_code_no_phone');
                $request->session()->put('reset_send_code_no_phone', '电话号码没有填！');
            }else{
                $request->session()->put('reset_send_code_no_phone', '电话号码没有填！');
            }
            return redirect()->back()->withInput();
        }else{
            if($request->session()->has('reset_send_phone')){
                $request->session()->forget('reset_send_phone');
                $request->session()->put('reset_send_phone',$request->get('phone'));
            }else{
                $request->session()->put('reset_send_phone',$request->get('phone'));
            }

            if($request->session()->has('isClickSendMessageReset')){
                $request->session()->forget('isClickSendMessageReset');
                $request->session()->put('isClickSendMessageReset', true);
            }else{
                $request->session()->put('isClickSendMessageReset', true);
            }
        }

        $resetCode = rand(100000, 999999);
        if($request->session()->has('reset_code')){
            $request->session()->forget('reset_code');
            $request->session()->put('reset_code', $resetCode);
        }else{
            $request->session()->put('reset_code', $resetCode);
        }

        $client = \Hprose\Client::create(config('app.HPROSE_IP'), false);
        $content = '您收到了创建kpi考勤系统的验证码，将其输入到验证码输入框。验证码为：'.$resetCode;
        $client->push_sms_store($request->get('phone'),$content);

        return redirect()->back()->withInput()->withErrors($validator);
    }

    public function resetCheckCode (Request $request){
        if($request->session()->has('reset_code')){
            $resetCode = $request->session()->get('reset_code');
//            $request->session()->forget('reset_code');
        }else{
            $resetCode = null;
        }

        if($request->session()->has('reset_send_phone')){
            $sendCodePhone = $request->session()->get('reset_send_phone');
//            $request->session()->forget('reset_send_phone');
        }else{
            $sendCodePhone = null;
        }

        if($sendCodePhone != $request->get('phone')){
            if($request->session()->has('reset_phone_changed')){
                $request->session()->forget('reset_phone_changed');
                $request->session()->put('reset_phone_changed','您要改密码的手机号和发送验证码的手机号不一样！');
            }else{
                $request->session()->put('reset_phone_changed','您要改密码的手机号和发送验证码的手机号不一样！');
            }
            return redirect()->back()->withInput();
        }

        if($resetCode != $request->get('code')){
            if($request->session()->has('reset_code_error')){
                $request->session()->forget('reset_code_error');
                $request->session()->put('reset_code_error','您的验证码错误！');
            }else{
                $request->session()->put('reset_code_error','您的验证码错误！');
            }
            return redirect()->back()->withInput();
        }

        return redirect('/reset_password');
    }

    public function resetPassword(Request $request){
        $title1 = '首页';
        $title2 = '重置密码';
        $titleLink1 = '/';
        $titleLink2 = '/reset_password';
        $resetPasswordNotSame = null;

        return view('resetPassword',compact('title1','title2','titleLink1','titleLink2','resetPasswordNotSame'));
    }

    public function newPasswordStore(Request $request){
        $validator = \Validator::make($request->all(), [
            'phone' => 'required|string|regex:"^1[0-9]{10}$"|exists:users',
            'password' => 'required|string|min:6|confirmed',
        ],['password.min' => '密码最少要6位数！',
            'password.confirmed' => '两次密码不一样！'
        ],[]);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
        }
        
        $phone = $request->get('phone');
        $password = $request->get('password');
        $user = User::where('phone', $phone)->first();
        $user->password = bcrypt($password);
        $user->save();
        $request->session()->forget('reset_send_phone');
        $request->session()->forget('reset_code');
        return redirect('/login');
    }
}