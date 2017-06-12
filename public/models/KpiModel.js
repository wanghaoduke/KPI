var resService = angular.module('resService',[]);
resService.factory('Kpi', ['$http', '$q',
    function($http, $q){
        var kpi = {};
        
        kpi.createAssessment = function (date){
            return $q(function(resolve, reject){
                $http({
                    method: 'PUT',
                    url: "/create_month_assessment",
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

        return kpi;
    }
]);