@extends('layouts.newApp')

@section('content')
    <div class="container">
        <div class="row">
            <div>
                <div class="panel panel-default">
                    <br>
                    <h3 style="text-align: center;">为企网考核系统</h3>
                    <br>
                    <hr>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="/reset_check_code">
                            {{ csrf_field() }}

                            <div class="form-group {{$sendCodeNoPhone ? ' has-error' : ''}} {{$resetPhoneChanged ? ' has-error' : ''}} {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="phone" class="col-md-4 control-label"><span style="color:red;">*</span>手机号</label>

                                <div class="col-md-4">
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required autofocus>

                                    @if ($sendCodeNoPhone)
                                        <span class="help-block">
                                        <strong>{{$sendCodeNoPhone}}</strong>
                                    </span>
                                    @endif
                                    @if ($resetPhoneChanged)
                                        <span class="help-block">
                                        <strong>{{$resetPhoneChanged}}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{$resetCodeError ? ' has-error' : ''}}">
                                <label for="code" class="col-md-4 control-label"><span style="color: red;">*</span>短信验证码</label>

                                <div class="col-md-3">
                                    <input id="code" type="text" class="form-control" name="code" placeholder="请输入您收到的验证码" required>

                                    @if ($resetCodeError)
                                        <span class="help-block">
                                        <strong>{{$resetCodeError}}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="col-md-1">
                                    <a class="btn btn-primary" href="#" onclick="event.preventDefault(); sendMessage()">获取验证码</a>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form action="/reset_send_code" method="POST" id="sendMessage">
                            {{ csrf_field() }}
                            <input type="hidden" name="phone" id="resetCopyPhone">
                            <button type="submit" id="sendMessageBtn" hidden></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sendMessage(){
            document.getElementById('resetCopyPhone').value = document.getElementById('phone').value;
            document.getElementById('sendMessage').submit();
        }
    </script>

@endsection