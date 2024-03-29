var resService = angular.module('resService', []);
resService.factory("AdminManage", ['$http', '$q',
    function($http, $q){
        var adminManage = {};

        //获取所有员工信息
        adminManage.getAllStaffs = function(value){
            return $q(function(resolve, reject){
                $http.get('/admin/staff_management?status=' + value)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };


        //改变员工的权限
        // adminManage.changeStaffJurisdiction = function(id,value){
        //     return $q(function(resolve, reject){
        //         $http.post('/admin/change_staff_jurisdiction/'+ id +'?jurisdiction='+ value)
        //             .success(function(data){
        //                 resolve(data);
        //             }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };
        
        //改变员工的状态
        // adminManage.changeStaffStatus = function(id,value){
        //     return $q(function(resolve, reject){
        //         $http.post('/admin/change_staff_status/'+ id +'?status='+ value)
        //             .success(function(data){
        //                 resolve(data);
        //             }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };

        //update raters
        adminManage.updateRaters = function(id, data){
            return $q(function(resolve, reject){
                $http({
                    method: 'PUT',
                    url: "/admin/raters_management/" + id,
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

        //保存默认评分百分比
        // adminManage.saveRaterPercentage = function(id, data){
        //     return $q(function(resolve, reject){
        //         $http({
        //             method: 'POST',
        //             url: "/admin/save_rater_percentage/" + id,
        //             headers: {
        //                 'Content-Type': 'application/x-www-form-urlencoded'
        //             },
        //             data: $.param(data)
        //         }).success(function(data){
        //             resolve(data);
        //         }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };

        //获取所有员工信息
        adminManage.getAllStaffsNoLeaveNoSelected = function(team){
            return $q(function(resolve, reject){
                $http({
                    method: "GET",
                    url: "/admin/raters_management/all_staffs_no_leave_no_selected?team=" + team,
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

        //改变管理员的是否为高级管理员的状态
        // adminManage.changeStaffIsSeniorManager = function(id, value){
        //     return $q(function(resolve, reject){
        //         $http({
        //             method: "POST",
        //             url: "/admin/change_staff_is_senior_manager/"+ id +'?is_senior_Manager='+ value,
        //             headers: {
        //                 'Content-Type': 'application/x-www-form-urlencoded'
        //             }
        //         })
        //             .success(function(data){
        //                 resolve(data);
        //             }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };
        
        //添加评论人
        adminManage.addNewRaters = function(data){
            return $q(function(resolve, reject){
                $http({
                    method: "PUT",
                    url: "/admin/raters_management/update_new_raters",
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

        //删除默认的评论员
        // adminManage.deleteDefaultRater = function(id, data){
        //     return $q(function(resolve, reject){
        //         $http({
        //             method: 'POST',
        //             url: "/admin/delete_default_rater/" + id,
        //             headers: {
        //                 'Content-Type': 'application/x-www-form-urlencoded'
        //             },
        //             data: $.param(data)
        //         }).success(function(data){
        //             resolve(data);
        //         }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };
        
        //获取默认策划组的评论员
        // adminManage.getPlanRater = function(){
        //     return $q(function(resolve, reject){
        //         $http.get('/admin/get_plan_rater')
        //             .success(function(data){
        //                 resolve(data);
        //             }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };

        //获取默认开发组的评论员
        // adminManage.getDevelopmentRater = function(){
        //     return $q(function(resolve, reject){
        //         $http.get('/admin/get_development_rater')
        //             .success(function(data){
        //                 resolve(data);
        //             }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };

        //获取默认的评论员
        adminManage.getDepartmentRater = function(department){
            return $q(function(resolve, reject){
                $http.get('/admin/raters_management?department='+department)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //获取全部assessment
        adminManage.getAllAssessmentsDetail = function(){
            return $q(function(resolve, reject){
                $http.get('/admin/assessment_management')
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //改变员工分组
        // adminManage.saveTheDepartment = function(id, department){
        //     return $q(function(resolve, reject){
        //         $http({
        //             method: "POST",
        //             url: "/admin/save_staff_department/"+ id + "?department=" + department,
        //             headers: {
        //                 'Content-Type': 'application/x-www-form-urlencoded'
        //             }
        //         })
        //             .success(function(data){
        //                 resolve(data);
        //             }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };

        //改变assessment状态
        adminManage.changeAssessmentCompleted = function(id, data){
            return $q(function(resolve, reject){
                $http({
                    method: "PUT",
                    url: "/admin/assessment_management/" + id,
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

        //删除assessment
        adminManage.deleteAssessment = function(id){
            return $q(function(resolve, reject){
                $http.delete('/admin/assessment_management/' + id)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //update users表内容
        adminManage.updateUsers = function(id, data){
            return $q(function(resolve, reject){
                $http({
                    method: "PUT",
                    url: "/admin/staff_management/" + id,
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
        //改变后台管理权限
        // adminManage.changeAdmin = function(id, value){
        //     return $q(function(resolve, reject){
        //         $http({
        //             method: "POST",
        //             url: "/admin/change_is_admin/" + id + "?is_admin=" + value,
        //             headers: {
        //                 'Content-Type': 'application/x-www-form-urlencoded'
        //             }
        //         })
        //             .success(function(data){
        //                 resolve(data);
        //             }).error(function(data){
        //             reject(data);
        //         });
        //     });
        // };

        return adminManage;
    }
]);