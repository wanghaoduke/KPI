<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>KPI考核系统</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery-1.8.3.min.js') }}"></script>
    <script src="http://cdn.static.runoob.com/libs/angular.js/1.4.6/angular.min.js"></script>
    <script src="http://apps.bdimg.com/libs/angular-route/1.3.13/angular-route.js"></script>
    <script src="{{ asset('js/ui-bootstrap-tpls-1.2.5.min.js') }}"></script>
    <script src="{{ asset('js/angular-locale_zh-cn.js') }}"></script>


</head>
<body style="background: gray;">
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
                    <div style="display: inline-block;">欢迎用户：{{ Auth::user()->name }}！</div>&nbsp;&nbsp;&nbsp;&nbsp;
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

@yield('content')


</body>
</html>