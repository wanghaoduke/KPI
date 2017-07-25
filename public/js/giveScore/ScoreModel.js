var resService = angular.module('resService', []);
resService.factory('Score', ['$http', '$q',
    function($http, $q){
        var score = {};

        //获取需要评分的记录
        score.getYourAssessment = function (){
            return $q(function(resolve, reject){
                $http.get('/score/get_your_assessment')
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //获取该月份考核的详情
        score.getStaffScores = function (assessmentId) {
            return $q(function(resolve, reject){
                $http.get('/score/get_staff_scores/' + assessmentId)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };
        
        //保存评分信息
        score.saveStaffScore = function (data) {
            return $q(function(resolve, reject){
                $http({
                    method: 'POST',
                    url: '/score/save_staff_scores/' + data['staff_score_id'],
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: $.param(data)
                })
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };
        
        //新评分保存
        score.saveTheStaffScore = function(id, data){
            return $q(function(resolve, reject){
                $http({
                    method: "POST",
                    url: "/score/save_the_staff_score/" + id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: $.param(data)
                })
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        return score;
    }
]);