var controllers = angular.module('controllers',[]);

controllers.controller('showAssessmentController', ['$scope', '$http', '$location', 'Score',
    function($scope, $http, $location, Score){
        
        //获取要评的记录
        Score.getYourAssessment().then(function(data){
            $scope.assessments = data;
        });
    }
]);

controllers.controller('giveScoreController', ['$scope', '$http', '$location', 'Score', '$routeParams', '$timeout',
    function($scope, $http, $location, Score, $routeParams, $timeout){
        $scope.isLastStaff = false;
        $scope.score20Array = [20,18,16,14,12,10,8,6,4,2,0];
        $scope.score30Array = [30,27,24,21,18,15,12,9,6,3,0];
        $scope.tempStaffScore = {};
        var i = 0;
        //获取该月份的要评分详情
        Score.getStaffScores($routeParams['id']).then(function(data){
            $scope.staffScores = data;
            $scope.staffLength = data.length;
            // console.log($scope.staffLength);
            $scope.tempStaffScore = $scope.staffScores[i];
        });

        //选择被评人的分数
        $scope.prototypeSelectScore = function (score){
            $scope.tempStaffScore.prototype = parseInt(score);
        };
        $scope.finishedProductSelectScore = function (score){
            $scope.tempStaffScore.finished_product = parseInt(score);
        };
        $scope.developmentQualitySelectScore = function (score){
            $scope.tempStaffScore.development_quality = parseInt(score);
        };
        $scope.developEfficiencySelectScore = function (score){
            $scope.tempStaffScore.develop_efficiency = parseInt(score);
        };
        $scope.abilitySelectScore = function (score){
            $scope.tempStaffScore.ability = parseInt(score);
        };
        $scope.responsibilitySelectScore = function (score){
            $scope.tempStaffScore.responsibility = parseInt(score);
        };

        //保存一个后显示下一个
        $scope.saveScore = function(){
            if($scope.tempStaffScore.department == 3){
                if(!($scope.tempStaffScore.prototype && $scope.tempStaffScore.finished_product && $scope.tempStaffScore.ability && $scope.tempStaffScore.responsibility)){
                    alert('您有部分评分项未评！');
                }else{
                    Score.saveStaffScore($scope.tempStaffScore).then(function(data){
                        i = i + 1;
                        if(i < $scope.staffLength - 1){
                            $scope.isLastStaff = false;
                            $scope.tempStaffScore = $scope.staffScores[i];
                            $scope.isComplete = false;
                        }
                        if(i == $scope.staffLength - 1){
                            $scope.isLastStaff = true;
                            $scope.tempStaffScore = $scope.staffScores[i];
                            $scope.isComplete = false;
                        }
                        if(i > $scope.staffLength -1){
                            $scope.isLastStaff = true;
                            $scope.isComplete = true;
                        }
                    });
                }
            }
            if($scope.tempStaffScore.department == 4){
                if(!($scope.tempStaffScore.development_quality >= 0 && $scope.tempStaffScore.develop_efficiency >= 0 && $scope.tempStaffScore.ability >= 0 && $scope.tempStaffScore.responsibility >= 0)){
                    alert('您有部分评分项未评！');
                }else{
                    Score.saveStaffScore($scope.tempStaffScore).then(function(data){
                        i = i + 1;
                        if(i < $scope.staffLength - 1){
                            $scope.isLastStaff = false;
                            $scope.tempStaffScore = $scope.staffScores[i];
                            $scope.isComplete = false;
                        }
                        if(i == $scope.staffLength - 1){
                            $scope.isLastStaff = true;
                            $scope.tempStaffScore = $scope.staffScores[i];
                            $scope.isComplete = false;
                        }
                        if(i > $scope.staffLength -1){
                            $scope.isLastStaff = true;
                            $scope.isComplete = true;
                            alert('当前评分已完成，谢谢！');
                        }
                    });
                }
            }
        };

        //取消
        $scope.cancel = function(){
            $location.url("/device/device?restore_grid=true");
        };
    }
]);