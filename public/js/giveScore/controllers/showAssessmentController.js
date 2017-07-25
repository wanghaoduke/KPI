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
        // $scope.isLastStaff = false;
        // $scope.score20Array = [20,18,16,14,12,10,8,6,4,2,0];
        // $scope.score30Array = [30,27,24,21,18,15,12,9,6,3,0];
        $scope.qualityScoreArr = [];
        for(var m = 30; m >= 0; m--){
            $scope.qualityScoreArr[30-m] = {name: m + "分", value: m};
        }
        $scope.attributeScoreArr = [];
        for(var m = 40; m >= 0; m--){
            $scope.attributeScoreArr[40-m] = {name: m + "分", value: m};
        }
        $scope.tempStaffScore = {};
        var i = 0;
        function getAllAssessmentDetails(){
            Score.getStaffScores($routeParams['id']).then(function(data){
                $scope.staffScores = data;
                // $scope.staffLength = data.length;
                // console.log($scope.staffLength);
                $scope.tempStaffScore = $scope.staffScores[i];
                for(var j = 0; j < $scope.staffScores.length; j++){
                    if($scope.staffScores[j]['quality_score'] || $scope.staffScores[j]['quality_score'] == 0){
                        $scope.staffScores[j]['selectQualityScore'] = false;
                    }else{
                        $scope.staffScores[j]['selectQualityScore'] = true;
                    }
                    if($scope.staffScores[j]['attitude_score'] || $scope.staffScores[j]['attitude_score'] == 0){
                        $scope.staffScores[j]['selectAttitudeScore'] = false;
                    }else{
                        $scope.staffScores[j]['selectAttitudeScore'] = true;
                    }
                }
            });
        }
        //获取该月份的要评分详情
        getAllAssessmentDetails();

        //保存评的分数
        $scope.saveScore = function(staffScoreId, item, score){
            var data = {};
            data['item'] = item;
            data['score'] = score;
            Score.saveTheStaffScore(staffScoreId, data).then(function(data){
                getAllAssessmentDetails();
            })
        };


        //改变是否可以选择函数
        $scope.changeQualityAbleSelect = function(id){
            for(var j = 0; j < $scope.staffScores.length; j++){
                if($scope.staffScores[j]['staff_score_id'] == id){
                    $scope.staffScores[j]['selectQualityScore'] = true;
                }
            }
        };
        $scope.changeAttitudeAbleSelect = function(id){
            for(var j = 0; j < $scope.staffScores.length; j++){
                if($scope.staffScores[j]['staff_score_id'] == id){
                    $scope.staffScores[j]['selectAttitudeScore'] = true;
                }
            }
        };
        // //选择被评人的分数
        // $scope.prototypeSelectScore = function (score){
        //     if(score || score == 0){
        //         $scope.tempStaffScore.prototype = parseInt(score);
        //     }else{
        //         $scope.tempStaffScore.prototype = null;
        //     }
        // };
        // $scope.finishedProductSelectScore = function (score){
        //     if(score || score == 0){
        //         $scope.tempStaffScore.finished_product = parseInt(score);
        //     }else{
        //         $scope.tempStaffScore.finished_product = null;
        //     }
        // };
        // $scope.developmentQualitySelectScore = function (score){
        //     if(score || score == 0){
        //         $scope.tempStaffScore.development_quality = parseInt(score);
        //     }else{
        //         $scope.tempStaffScore.development_quality = null;
        //     }
        // };
        // $scope.developEfficiencySelectScore = function (score){
        //     if(score || score == 0){
        //         $scope.tempStaffScore.develop_efficiency = parseInt(score);
        //     }else{
        //         $scope.tempStaffScore.develop_efficiency = null;
        //     }
        // };
        // $scope.abilitySelectScore = function (score){
        //     if(score || score == 0){
        //         $scope.tempStaffScore.ability = parseInt(score);
        //     }else{
        //         $scope.tempStaffScore.ability = null;
        //     }
        // };
        // $scope.responsibilitySelectScore = function (score){
        //     if(score || score == 0){
        //         $scope.tempStaffScore.responsibility = parseInt(score);
        //     }else{
        //         $scope.tempStaffScore.responsibility = null;
        //     }
        // };

        // //保存一个后显示下一个
        // $scope.saveScore = function(){
        //     if($scope.tempStaffScore.department == 3){
        //         if(!(($scope.tempStaffScore.prototype || $scope.tempStaffScore.prototype == 0) && ($scope.tempStaffScore.finished_product || $scope.tempStaffScore.finished_product == 0) && ($scope.tempStaffScore.ability || $scope.tempStaffScore.ability == 0) && ($scope.tempStaffScore.responsibility || $scope.tempStaffScore.responsibility == 0))){
        //             alert('您有部分评分项未评！');
        //         }else{
        //             Score.saveStaffScore($scope.tempStaffScore).then(function(data){
        //                 i = i + 1;
        //                 if(i < $scope.staffLength - 1){
        //                     $scope.isLastStaff = false;
        //                     $scope.tempStaffScore = $scope.staffScores[i];
        //                     $scope.isComplete = false;
        //                 }
        //                 if(i == $scope.staffLength - 1){
        //                     $scope.isLastStaff = true;
        //                     $scope.tempStaffScore = $scope.staffScores[i];
        //                     $scope.isComplete = false;
        //                 }
        //                 if(i > $scope.staffLength -1){
        //                     $scope.isLastStaff = true;
        //                     $scope.isComplete = true;
        //                     $location.url("/");
        //                 }
        //             });
        //         }
        //     }
        //     if($scope.tempStaffScore.department == 4){
        //         if(!(($scope.tempStaffScore.development_quality || $scope.tempStaffScore.development_quality == 0) && ($scope.tempStaffScore.develop_efficiency || $scope.tempStaffScore.develop_efficiency == 0) && ($scope.tempStaffScore.ability || $scope.tempStaffScore.ability ==0) && ($scope.tempStaffScore.responsibility || $scope.tempStaffScore.responsibility ==0))){
        //             alert('您有部分评分项未评！');
        //         }else{
        //             Score.saveStaffScore($scope.tempStaffScore).then(function(data){
        //                 i = i + 1;
        //                 if(i < $scope.staffLength - 1){
        //                     $scope.isLastStaff = false;
        //                     $scope.tempStaffScore = $scope.staffScores[i];
        //                     $scope.isComplete = false;
        //                 }
        //                 if(i == $scope.staffLength - 1){
        //                     $scope.isLastStaff = true;
        //                     $scope.tempStaffScore = $scope.staffScores[i];
        //                     $scope.isComplete = false;
        //                 }
        //                 if(i > $scope.staffLength -1){
        //                     $scope.isLastStaff = true;
        //                     $scope.isComplete = true;
        //                     $location.url("/");
        //                 }
        //             });
        //         }
        //     }
        // };

        //取消
        $scope.cancel = function(){
            $location.url("/");
        };
    }
]);