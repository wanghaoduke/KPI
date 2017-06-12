@extends('layouts.newApp')

@section('content')
    <div class="container">
        <div class="row">
            <div class="panel panel-default">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <br>
                <br>
                <div style="text-align: center;">
                    <a class="btn btn-primary" style="font-size: 20px; margin: 5px;">评分查询</a><a class="btn btn-primary" style="font-size: 20px; margin: 5px;">进入系统</a><a href="/assessment_manage" class="btn btn-primary" style="font-size: 20px; margin: 5px;">考核管理</a>
                </div>
                <br>
                <hr>
            </div>
        </div>
    </div>

@endsection