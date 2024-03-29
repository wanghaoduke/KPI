@extends('layouts.assessmentApp')

@section('content')
    <div ng-app="giveScoreApp">
        <div class="container" style="background: white;" class="col-md-8 col-md-offset-2">
            <br>
            <div class="container">
                <nav>
                    <ul style="display: inline-block; list-style-type: none;">
                        <li style=" float: left; padding-right: 15px;"><a href="/" style="text-decoration: none;">为企网考核系统</a></li>
                        @if($title1)
                            <li style=" float: left; padding-right: 15px;">>></li>
                            <li style=" float: left; padding-right: 15px;"><a href="{{$titleLink1}}">{{ $title1 }}</a></li>
                        @endif
                        @if($title2)
                            <li style=" float: left; padding-right: 15px;">>></li>
                            <li style=" float: left; padding-right: 15px;"><a href="{{$titleLink2}}">{{ $title2 }}</a></li>
                        @endif
                    </ul>
                    <div style="display: inline-block; position: relative; right: 5px; float: right;">
                        @if(Auth::check())
                            <div style="display: inline-block;">欢迎用户：{{ Auth::user()->name }}！</div>
                            @if(Auth::user()->is_admin == 1)
                                <div style="display: inline-block;"><a href="/admin" class="btn btn-primary">员工管理</a></div>
                            @else
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            @endif
                            <div style="display: inline-block;"><a href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit()" class="btn btn-danger">注销</a></div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                {{ csrf_field() }}
                            </form>
                        @else
                            <div style="display: inline-block;"><a href="{{ route('login') }}" class="btn btn-primary">登录</a></div> &nbsp;&nbsp;&nbsp;&nbsp;
                            <div style="display: inline-block;"><a href="{{ route('register') }}" class="btn btn-primary">注册</a></div>
                        @endif
                    </div>
                </nav>
            </div>
        </div>
        <div  ng-view ></div>
    </div>

    <script src="{{ asset('angular-ui-select/dist/select.js') }}"></script>
    <link href="{{ asset('angular-ui-select/dist/select.css') }}" rel="stylesheet" type="text/css" >
    <script src="{{ asset('js/giveScore/app.js') }}"></script>
    <script src="{{ asset('js/giveScore/giveScoreRoute.js') }}"></script>
    <script src="{{ asset('js/giveScore/controllers/showAssessmentController.js') }}"></script>
    <script src="{{ asset('js/giveScore/ScoreModel.js') }}"></script>
@endsection