var resService = angular.module('resService', []);
resService.factory('ShowScore', ['$http', '$q',
    function($http, $q){
        var showScore = {};

        showScore.getLastAssessmentDate = function (){
            return $q(function(resolve, reject){
                $http.get('/show_score/get_last_assessment_date')
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        showScore.getPeriodAllScores = function (data){
            return $q(function(resolve, reject){
                $http({
                    method: 'GET',
                    url: "/show_score?startDate="+data.startDate+"&&startDateStr="+data.startDateStr+"&&endDateStr="+data.endDateStr+"&&endDate="+data.endDate+"&&department="+data.department+"&&item="+data.item,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).success(function(data){
                    resolve(data);
                }).error(function(data){
                    reject(data);
                });
            });
        };

        return showScore;
    }
]);