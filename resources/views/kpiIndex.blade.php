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
                        <a class="btn btn-primary" style="font-size: 20px; margin: 5px;">评分查询</a><a class="btn btn-primary" href="/score/master" style="font-size: 20px; margin: 5px;">进入系统</a><a href="/assessment_manage" class="btn btn-primary" style="font-size: 20px; margin: 5px;">考核管理</a>
                    </div>
                    <br>
                    <br>
                   <div class="row">
                       @if(count($planScores) > 0)
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
                       @else
                           <div class="col-md-4 col-md-offset-1">
                               <h4>策划组暂时还没有数据！</h4>
                           </div>
                       @endif
                       @if(count($developmentScores) > 0)
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
                           <div class="col-md-4 col-md-offset-1">
                               <h4>开发组暂时还没有数据！</h4>
                           </div>
                       @endif
                   </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>

@endsection