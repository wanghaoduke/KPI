var resService = angular.module('resService', []);
resService.factory('ShowScore', ['$http', '$q',
    function($http, $q){
        var showScore = {};

        showScore.getLastAssessmentDate = function (){
            return $q(function(resolve, reject){
                $http.get('/get_last_assessment_date')
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
                    method: 'POST',
                    url: "/get_period_all_scores",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: $.param(data)
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