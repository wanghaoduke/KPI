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
                        <form class="form-horizontal" role="form" method="POST" action="/new_password_store">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label class="col-md-4 control-label"><span style="color:red;">*</span>手机号</label>

                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="showPhone" disabled value="{{ session('reset_send_phone') }}" required autofocus>
                                </div>
                            </div>
                            <input type="text" hidden name="phone" value="{{session('reset_send_phone')}}">

                            <div class="form-group {{$resetPasswordNotSame ? ' has-error' : ''}} {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label"><span style="color:red;">*</span>登录密码</label>

                                <div class="col-md-4">
                                    <input id="password" type="password" class="form-control" name="password" required>

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
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
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

@endsection