
@extends('layouts.newApp')

@section('content')
    {{--<script type="text/javascript" src="/jsdate/WdatePicker.js"></script>--}}
    <div class="container" ng-controller="CreateAssessmentDetailController">
        <div class="row">
            <div class="panel panel-default">
                <br>
                <input  ng-model="assessment.id" type="text" value="{{ $id }}">
                {{$id}}
                <h3 style="text-align: center;">添加月度考核</h3>
                <br><br>
                <div class="panel-body">
                    <div class="row">
                            <h4 class="col-md-3">考核月份</h4>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            {{--<input class="form-control Wdate" style="height: 35px;" type="text" id="createDate" name="createDate" onfocus="WdatePicker({dateFmt:'yyyy-MM'})">--}}
                        </div>
                    </div>
                    <br><br>

                    <div class="row">
                        <h4 class="col-md-3">策划组参评设置</h4>
                    </div>

                    @foreach($planUsers as $planUser)
                        <div class="col-md-5" style="background: #eaeaea; margin: 12px 38px; font-size: 25px; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; height: 50px;">
                            {{$planUser['name']}} &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 13px;">参评：</span>
                        </div>
                    @endforeach
                    <br>

                    <div class="row"></div>
                    <div class="row">
                        <h4 class="col-md-3">开发组参评设置</h4>
                    </div>

                    @foreach($developmentUsers as $developmentUser)
                        <div class="col-md-5" style="background: #eaeaea; margin: 12px 38px; font-size: 25px; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; height: 50px;">
                            {{$developmentUser['name']}} &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 13px;">参评：</span>
                        </div>
                    @endforeach

                    <div class="row"></div>
                    <div class="row" style="text-align: center;">
                        <br><br>
                        <a class="btn btn-primary" style="padding: 10px 50px; margin-right: 38px;" ng-click="ok()">确认</a>
                        <a class="btn btn-default" href="/assessment_manage" style="padding: 10px 50px; background: #eaeaea;">取消</a>
                        <br>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection