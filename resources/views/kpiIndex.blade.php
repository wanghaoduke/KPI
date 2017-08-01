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
                <div class="panel-body">
                    <br>
                    <br>
                    <div style="text-align: center;">
                        <a class="btn btn-primary" href="/show_score/index" style="font-size: 20px; margin: 5px;">评分查询</a>

                        @if(Auth::check() && $count > 0)
                            <a class="btn btn-primary" href="/score/master" style="font-size: 20px; margin: 5px;">进入系统</a>
                        @endif

                        @if(Auth::check() && auth()->user()->Jurisdiction == 1)
                            <a href="/assessment_manage" class="btn btn-primary" style="font-size: 20px; margin: 5px;">考核管理</a>
                        @endif

                        @if(Auth::check())
                            @if(auth()->user()->is_senior_manager == 1)
                                <a href="/advices#/rater/index" class="btn btn-primary" style="font-size: 20px; margin: 5px;">合理化建议</a>
                            @else
                                <a href="/advices" class="btn btn-primary" style="font-size: 20px; margin: 5px;">合理化建议</a>
                            @endif
                        @endif

                    </div>
                    <br>
                    <br>
                   <div class="row">
                       @if($assessment['id'])
                           <div class="col-md-4 col-md-offset-1">
                           <h3>策划组排行 &nbsp;&nbsp;&nbsp;<span style="font-size: 15px; color: #CCCCCC;">{{ $assessment['year'] }}年{{ $assessment['month'] }}月份</span></h3>
                           <table class="table table-bordered">
                           <tr>
                           <th style="text-align: center;">排名</th>
                           <th style="text-align: center;">姓名</th>
                           <th style="text-align: center;">平均得分数</th>
                           </tr>
                           @for($i = 0; $i < count($planScores); $i++)
                           <tr>
                           <th style="text-align: center;">{{ $i + 1 }}</th>
                           <th style="text-align: center;">{{ $planScores[$i]['name'] }}</th>
                           <th style="text-align: center;">{{ $planScores[$i]['score'] }}</th>
                           </tr>
                           @endfor
                           </table>
                           </div>
                           <div class="col-md-4 col-md-offset-1">
                           <h3>开发组排行 &nbsp;&nbsp;&nbsp;<span style="font-size: 15px; color: #CCCCCC;">{{ $assessment['year'] }}年{{ $assessment['month'] }}月份</span></h3>
                           <table class="table table-bordered">
                               <tr>
                                 <th style="text-align: center;">排名</th>
                           <th style="text-align: center;">姓名</th>
                           <th style="text-align: center;">平均得分数</th>
                           </tr>
                           @for($i = 0; $i < count($developmentScores); $i++)
                           <tr>
                           <th style="text-align: center;">{{ $i + 1 }}</th>
                           <th style="text-align: center;">{{ $developmentScores[$i]['name'] }}</th>
                           <th style="text-align: center;">{{ $developmentScores[$i]['score'] }}</th>
                           </tr>
                           @endfor
                           </table>
                           </div>
                       @else
                           <h3 style="text-align: center;">没有任何已评完数据</h3>
                       @endif
                   </div>
                    <div class="row">
                        <a href="/rules_down" class="col-md-2 col-md-offset-8" style="text-align: right;">评分规则</a>
                    </div>
                    <br>

                    {{--<div class="row">--}}
                        {{--<div style="display: block; background: #e6e6e6; width: 390px; margin: 0 auto; padding: 28px 20px; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;">--}}
                            {{--<h4>分配比例：</h4>--}}
                            {{--<h5>部门奖金总额：人员数量20人*600元=约12000元</h5>--}}
                            {{--<h5>小组奖池分配：策划约4成/开发约6成，具体视情况而定</h5>--}}
                            {{--<h5>人员奖励规则：最后 1~3 名无奖励，其他按名次分配比例</h5>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
    </div>

@endsection