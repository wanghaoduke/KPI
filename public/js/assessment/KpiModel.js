var resService = angular.module('resService',[]);
resService.factory('Kpi', ['$http', '$q',
    function($http, $q){
        var kpi = {};

        kpi.createAssessment = function (data){
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

        kpi.getStaffDetail = function(id){
            return $q(function(resolve, reject){
                $http.get('/get_assessment_detail/'+id)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        kpi.getRaters = function(data){
            return $q(function(resolve, reject){
                // $http.get('/get_raters/'+data.staff_id)
                $http({
                    method: 'POST',
                    url: '/get_raters/'+data.staff_id,
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

        //获取所有员工 除了已经离职的
        kpi.getAllStaffs = function(){
            return $q(function(resolve, reject){
                $http.get('/get_all_staffs')
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };
        
        //获取所有被选员工的详情
        kpi.getSelectedStaffDetails = function(assessmentId ,data){
            return $q(function(resolve, reject){
                $http({
                    method: 'POST',
                    url: '/get_selected_staff_details/' + assessmentId,
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

        kpi.editRaters = function(assessmentId, data){
            return $q(function(resolve, reject){
                $http({
                    method: 'POST',
                    url: '/edit_raters/' + assessmentId,
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

        return kpi;
    }
]);