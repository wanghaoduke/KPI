var controllers = angular.module('controllers', []);

controllers.controller('ShowScoresController', ['$scope', 'ShowScore', '$routeParams',
    function($scope, ShowScore, $routeParams){

        // $scope.startDate = new Date();
        $scope.department = 'all';
        $scope.item = 'all';
        $scope.itemName = '全部项目';
        $scope.showErrorTip = false;

        //item改变后数据显示改变
        $scope.changeTableDatas = function (item, datas) {
            switch(item){
                case 'all':
                    $scope.itemName = '全部项目';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['sumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['avgScore'];
                    }
                    break;
                case 'advices':
                    $scope.itemName = '建议评分';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['advicesSumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['advicesAvgScore'];
                    }
                    break;
                case 'quality':
                    $scope.itemName = '质量评分';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['qualitySumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['qualityAvgScore'];
                    }
                    break;
                case 'attitude':
                    $scope.itemName = '态度评分';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['attitudeSumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['attitudeAvgScore'];
                    }
                    break;
                case 'prototype':
                    $scope.itemName = '原型质量';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['prototypeSumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['prototypeAvgScore'];
                    }
                    break;
                case 'finishedProduct':
                    $scope.itemName = '成品质量';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['finishedProductSumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['finishedProductAvgScore'];
                    }
                    break;
                case 'developmentQuality':
                    $scope.itemName = '开发质量';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['developmentQualitySumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['developmentQualityAvgScore'];
                    }
                    break;
                case 'developEfficiency':
                    $scope.itemName = '开发效率';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['developEfficiencySumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['developEfficiencyAvgScore'];
                    }
                    break;
                case 'ability':
                    $scope.itemName = '能力与学习';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['abilitySumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['abilityAvgScore'];
                    }
                    break;
                case 'responsibility':
                    $scope.tableDatas = {};
                    $scope.itemName = '责任心';
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['responsibilitySumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['responsibilityAvgScore'];
                    }
                    break;
                default:
                    $scope.itemName = '全部项目';
                    $scope.tableDatas = {};
                    for(var i = 0; i < datas.length; i++){
                        $scope.tableDatas[i] = {};
                        $scope.tableDatas[i]['name'] = datas[i]['name'];
                        $scope.tableDatas[i]['department'] = datas[i]['department'];
                        $scope.tableDatas[i]['sumScore'] = datas[i]['sumScore'];
                        $scope.tableDatas[i]['avgScore'] = datas[i]['avgScore'];
                    }

            }
        };

        //获取需要显示的数据
        $scope.getShowData = function (){
            var startDateYear = $scope.startDate.getFullYear();
            var startDateMonth = $scope.startDate.getMonth() + 1;
            var endDateYear = $scope.endDate.getFullYear();
            var endDateMonth = $scope.endDate.getMonth() + 1;
            var startDate = startDateYear * 12 + startDateMonth;
            var endDate = endDateYear * 12 + endDateMonth;
            if(startDateMonth < 10){
                var tempStartDateMonth = "0" + startDateMonth;
            }else{
                var tempStartDateMonth = startDateMonth;
            }
            if(endDateMonth < 10){
                var tempEndDateMonth = "0" + endDateMonth;
            }else{
                var tempEndDateMonth = endDateMonth;
            }
            var startDateStr = startDateYear + "-" + tempStartDateMonth;
            var endDateStr = startDateYear + "-" + tempEndDateMonth;
            ShowScore.getPeriodAllScores({'startDate': startDate, 'startDateStr': startDateStr, 'endDateStr': endDateStr, 'endDate': endDate, 'department': $scope.department, 'item': $scope.item}).then(function(data){
                $scope.showErrorTip = false;
                $scope.allDatas = data['data'];
                $scope.changeTableDatas($scope.item, $scope.allDatas);
                // console.log($scope.tableDatas);
            },function(data){
                $scope.showErrorTip = true;
                $scope.errors = data;
                $scope.allDatas = {};
                $scope.changeTableDatas($scope.item, $scope.allDatas);
            });
        };

        //初始化时候的日期
        if($routeParams.searchDate){
            $scope.showErrorTip = false;
            $scope.startDate = new Date($routeParams.searchDate);
            $scope.endDate = new Date($routeParams.searchDate);
            // console.log($scope.startDate);
            $scope.getShowData();
        }else{
            ShowScore.getLastAssessmentDate().then(function(data){
                $scope.showErrorTip = false;
                $scope.startDate = new Date(data);
                $scope.endDate = new Date(data);
                // console.log($scope.startDate);
                $scope.getShowData();
            },function(data){
                $scope.showErrorTip = true;
                $scope.startDate = new Date();
                $scope.endDate = new Date();
                $scope.errors = data;
            });
        }
        //日历的打开
        $scope.startOpen = function($event){
            $event.preventDefault();
            $event.stopPropagation();
            $scope.startOpened = true;
        };
        $scope.endOpen = function($event){
            $event.preventDefault();
            $event.stopPropagation();
            $scope.endOpened = true;
        };




        //换item
        $scope.itemChange = function(newItem){
            $scope.item = newItem;
            $scope.getShowData();
        };

        //日期的查询
        $scope.searchPeriod = function(){
            $scope.getShowData();
        };

        //改变部门函数
        $scope.changeDepartment = function (newDepartment){
            $scope.department = newDepartment;
            // console.log($scope.department);
            $scope.getShowData();
        };


        //日历的参数
        $scope.options = {
            datepickerMode: "'month'",
            minMode: "month"
        };
    }
]);