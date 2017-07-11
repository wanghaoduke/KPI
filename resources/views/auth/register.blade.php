@extends('layouts.newApp')

@section('content')
<div class="container">
    <div class="row">
        <div>
            <div class="panel panel-default">
                {{--<div class="panel-heading">Register</div>--}}
                <br>
                <h3 style="text-align: center;">为企网考核系统</h3>
                <br>
                <hr>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group {{ $errors->has('phone') ?'has-error':'' }} {{$sendMessageError ? 'has-error' : ''}} {{$phoneChanged ? 'has-error' : ''}}">
                            <label for="phone" class="col-md-4 control-label"><span style="color:red;">*</span>手机号</label>

                            <div class="col-md-4">
                                <input id="phone" type="text" class="form-control" name="phone" value="{{ $phone or old('phone')}}" placeholder="请输入您的手机号" required>

                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                                @endif
                                @if ($sendMessageError)
                                    <span class="help-block">
                                    <strong>{{ $sendMessageError }}</strong>
                                </span>
                                @endif
                                @if ($phoneChanged)
                                    <span class="help-block">
                                    <strong>{{ $phoneChanged }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{$authenticationCodeError ? 'has-error' : ''}}">
                            <label for="authenticationCode" class="col-md-4 control-label"><span style="color:red;">*</span>短信验证码</label>

                            <div class="col-md-3">
                                <input id="authenticationCode" type="text" class="form-control" name="authenticationCode" placeholder="请输入您收到的验证码" required>
                                @if ($authenticationCodeError)
                                    <span class="help-block">
                                    <strong>{{ $authenticationCodeError }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="col-md-1" id="isShow1">
                                <a class="btn btn-primary" href="#" onclick="event.preventDefault(); sendMessage()" style="position: relative; right: 25px;">获取验证码</a>
                            </div>
                            {{--<a id="isShow1" class="btn btn-primary col-md-1" href="#" onclick="event.preventDefault(); sendMessage()" style="position: relative; right: 11px;">获取验证码</a>--}}
                            @if(!$isClickSendMessage)
                                <p id="isClickSend" hidden>notSend</p>
                            @endif
                            {{--<a id="isShow2" class="btn btn-primary col-md-1" href="#" onclick="event.preventDefault();" style="position: relative; right: 11px;">获取验证码</a>--}}
                            <div class="col-md-1" id="isShow2">
                                <a class="btn btn-primary" href="#" onclick="event.preventDefault();" id="sendMessageA" style="position: relative; right: 25px;">获取验证码</a>
                            </div>
                            @if($isClickSendMessage)
                                <p id="isClickSend" hidden>isSend</p>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label"><span style="color:red;">*</span>真实姓名</label>

                            <div class="col-md-4">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $name or old('name') }}" placeholder="请输入您的姓名" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('department') ? 'has-error' : ''}}">
                            <label for="department" class="col-md-4 control-label"><span style="color: red;">*</span>所属工作组</label>

                            <div class="col-md-4">
                                <select id="department" class="form-control" placeholder="请输入您的姓名" name="department" required>
                                    <option value="" selected="true" disabled="true">请选择您的组别</option>
                                    <option value="1">产品经理组</option>
                                    <option value="2">技术主管组</option>
                                    <option value="3">策划组</option>
                                    <option value="4">开发组</option>
                                </select>
                            </div>
                        </div>

                        {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">--}}
                            {{--<label for="email" class="col-md-4 control-label">E-Mail Address</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>--}}

                                {{--@if ($errors->has('email'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('email') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label"><span style="color:red;">*</span>登录密码</label>

                            <div class="col-md-4">
                                <input id="password" type="password" class="form-control" value="{{ $password }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label"><span style="color:red;">*</span>密码确认</label>

                            <div class="col-md-4">
                                <input id="password-confirm" type="password" value="{{ $passwordConfirm }}" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <button type="submit" class="btn btn-primary col-md-12">
                                    注册
                                </button>
                            </div>
                        </div>
                    </form>
                    <div style="text-align: center;">
                        <a href="/login" style="text-decoration: none; position: relative; right: 50px;">已有账号？立即登陆</a>
                    </div>
                    <form action="/send_message" method="POST" id="sendMessage">
                        {{ csrf_field() }}
                        <input type="hidden" name="phone" id="copyPhone">
                        <input type="hidden" name="name" id="copyName">
                        <input type="hidden" name="department" id="copyDepartment">
                        <input type="hidden" name="password" id="copyPassword">
                        <input type="hidden" name="password-confirm" id="copyPasswordConfirm">
                        <button type="submit" id="sendMessageBtn" hidden></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function sendMessage(){
        document.getElementById('copyPhone').value = document.getElementById('phone').value;
        document.getElementById('copyName').value = document.getElementById('name').value;
        document.getElementById('copyDepartment').value = document.getElementById('department').value;
        document.getElementById('copyPassword').value = document.getElementById('password').value;
        document.getElementById('copyPasswordConfirm').value = document.getElementById('password-confirm').value;
        document.getElementById('sendMessage').submit();
    }
    var isSend = document.getElementById('isClickSend');
    function showNext(a){
        if(a > 0){
            setTimeout(function(){
                showText.innerHTML = a + "秒";
                showNext(a-1);
            }, 1000);
        }else{
            document.getElementById('isShow1').style.display = "block";
            document.getElementById('isShow2').style.display = "none";
        }
    }
    if(isSend.innerHTML == 'isSend'){
        document.getElementById('isShow1').style.display = "none";
        document.getElementById('isShow2').style.display = "block";
        var showText = document.getElementById('sendMessageA');
//        var i = 60;
        showNext(60);
    }else{
        document.getElementById('isShow1').style.display = "block";
        document.getElementById('isShow2').style.display = "none";
    }
</script>
@endsection
