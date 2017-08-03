var resService = angular.module('resService',[]);
resService.factory('Kpi', ['$http', '$q',
    function($http, $q){
        var kpi = {};

        kpi.createAssessment = function (data){
            return $q(function(resolve, reject){
                $http({
                    method: 'POST',
                    url: "/assessment",
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
                $http.get('/assessment/'+id)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        kpi.getRaters = function(staff_id, assessment_id){
            return $q(function(resolve, reject){
                // $http.get('/get_raters/'+data.staff_id)
                $http({
                    method: 'GET',
                    url: '/raters/all_raters/'+staff_id + '?assessment_id=' + assessment_id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
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
                $http.get('/raters')
                    
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //获取所有的assessment
        kpi.getAllAssessments = function (){
            return $q(function(resolve, reject){
                $http.get('/assessment/get_all_assessments')
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };
        
        //获取所有被选员工的详情
        kpi.getSelectedStaffDetails = function(assessmentId ,staffId){
            return $q(function(resolve, reject){
                $http({
                    method: 'GET',
                    url: '/selected_staff_details/' + assessmentId + '?staffId=' + staffId,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
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
                    method: 'PUT',
                    url: '/raters/' + assessmentId,
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

        //改变考核是否完成状态
        kpi.changeAssessmentStatus = function(assessmentId){
            return $q(function(resolve, reject){
                $http({
                    method: "PUT",
                    url: "/assessment/" + assessmentId,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){

                });
            });
        };

        return kpi;
    }
]);