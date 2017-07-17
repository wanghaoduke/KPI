var controllers = angular.module('controllers', []);

controllers.controller("AdminIndexController", ['$scope',
    function($scope){

    }
]);

controllers.controller("StaffManageController", ["$scope", "AdminManage",
    function($scope, AdminManage){

        //获取全体员工信息
        AdminManage.getAllStaffs().then(function(data){
            if(data.length){
                console.log(1);
                $scope.hasStaffData = true;
            }else{
                console.log(2);
                $scope.hasStaffData = false;
            }
            $scope.staffDatas = data;
        });

        //改变员工的进入考核权限
        $scope.changeJurisdiction = function(id, value){
            AdminManage.changeStaffJurisdiction(id,value).then(function(data){
                AdminManage.getAllStaffs().then(function(data){
                    if(data.length){
                        $scope.hasStaffData = true;
                    }else{
                        $scope.hasStaffData = false;
                    }
                    $scope.staffDatas = data;
                });
            })
        };

        //改变员工的是否为高级管理员
        $scope.changeManagerStatus = function(id, value){
            AdminManage.changeStaffIsSeniorManager(id,value).then(function(data){
                AdminManage.getAllStaffs().then(function(data){
                    if(data.length){
                        $scope.hasStaffData = true;
                    }else{
                        $scope.hasStaffData = false;
                    }
                    $scope.staffDatas = data;
                });
            });
        };
        
        //改变员工的状态
        $scope.changeStatus = function(id, value){
            AdminManage.changeStaffStatus(id,value).then(function(data){
                AdminManage.getAllStaffs().then(function(data){
                    if(data.length){
                        $scope.hasStaffData = true;
                    }else{
                        $scope.hasStaffData = false;
                    }
                    $scope.staffDatas = data;
                });
            })
        }
    }
]);

controllers.controller("RaterSetController", ['$scope', 'AdminManage', '$uibModal',
    function($scope, AdminManage, $uibModal){
        //获取默认策划的评论员信息
        AdminManage.getPlanRater().then(function(data){
            $scope.planRaters = data;
            for(var i = 0; i < $scope.planRaters.length; i++){
                $scope.planRaters[i]['is_edit'] = false;
            }
        });
        
        //获取默认开发的评论员信息
        AdminManage.getDevelopmentRater().then(function(data){
            $scope.developmentRaters = data;
            for(var i = 0; i < $scope.developmentRaters.length; i++){
                $scope.developmentRaters[i]['is_edit'] = false;
            }
        });
        
        //让百分比可以修改
        $scope.letPercentageEdit = function(id, team){
            if(team == 'plan'){
                for(var i = 0; i < $scope.planRaters.length; i++){
                    if(id == $scope.planRaters[i]['id']){
                        $scope.planRaters[i]['is_edit'] = true;
                    }
                }
            }
            if(team == 'development'){
                for(var i = 0; i < $scope.developmentRaters.length; i++){
                    if(id == $scope.developmentRaters[i]['id']){
                        $scope.developmentRaters[i]['is_edit'] = true;
                    }
                }
            }
        };

        //保存percentage
        $scope.savePercentage = function(id, team, percentage){

            if(isNaN(percentage)){
                alert('百分比必须是数字！');
            }else{
                AdminManage.saveRaterPercentage(id, {'team': team, 'percentage': percentage}).then(function(data){

                    if(team == 'plan'){
                        for(var i = 0; i < $scope.planRaters.length; i++){
                            if(id == $scope.planRaters[i]['id']){
                                $scope.planRaters[i]['is_edit'] = false;
                            }
                        }
                    }
                    if(team == 'development'){
                        for(var i = 0; i < $scope.developmentRaters.length; i++){
                            if(id == $scope.developmentRaters[i]['id']){
                                $scope.developmentRaters[i]['is_edit'] = false;
                            }
                        }
                    }
                });
            }
        };

        //删除默认的评论员
        $scope.deleteRater = function(id, team){
            AdminManage.deleteDefaultRater(id,{'id': id, "team": team}).then(function(data){
                if(team == 'plan'){
                    AdminManage.getPlanRater().then(function(data){
                        $scope.planRaters = data;
                        for(var i = 0; i < $scope.planRaters.length; i++){
                            $scope.planRaters[i]['is_edit'] = false;
                        }
                    });
                }
                if(team == "development"){
                    AdminManage.getDevelopmentRater().then(function(data){
                        $scope.developmentRaters = data;
                        for(var i = 0; i < $scope.developmentRaters.length; i++){
                            $scope.developmentRaters[i]['is_edit'] = false;
                        }
                    });
                }
            });
        }
        
        //点击打开弹出页面选择员工
        $scope.addRater = function(team){
            var modalInstance = $uibModal.open({
                templateUrl: 'views/adminAddRaters.html',
                controller: 'AddRaterController',
                size: 'lg',
                resolve: {
                    selectedRaters: function(){
                        if(team == 'plan'){
                            return $scope.planRaters;
                        }else{
                            return $scope.developmentRaters;
                        }
                    },
                    team: function(){
                        return team;
                    }
                }
            });
            modalInstance.result.then(function(result){
                //获取默认策划的评论员信息
                AdminManage.getPlanRater().then(function(data){
                    $scope.planRaters = data;
                    for(var i = 0; i < $scope.planRaters.length; i++){
                        $scope.planRaters[i]['is_edit'] = false;
                    }
                });
                //获取默认开发的评论员信息
                AdminManage.getDevelopmentRater().then(function(data){
                    $scope.developmentRaters = data;
                    for(var i = 0; i < $scope.developmentRaters.length; i++){
                        $scope.developmentRaters[i]['is_edit'] = false;
                    }
                });
            });
        };
    }
]);

controllers.controller('AddRaterController', ['$scope', '$uibModalInstance', 'AdminManage', '$filter', 'selectedRaters', 'team',
    function($scope, $uibModalInstance, AdminManage, $filter, selectedRaters, team){
        $scope.selectedStaffIds = [];
        $scope.selectedStaffs = [];
        $scope.newAddStaffs = [];
        
        if(selectedRaters.length > 0){
            angular.forEach(selectedRaters, function(rater){
               $scope.selectedStaffIds.push(rater.id);
            });
            $scope.selectedStaffs = angular.copy(selectedRaters);
        }
        
        AdminManage.getAllStaffsNoLeaveNoSelected({'selectedIds': $scope.selectedStaffIds}).then(function(data){
            $scope.allStaffs = [];   //备选所有员工
            $scope.allStaffs = data;
            $scope.itemsPerPage = 12;
            $scope.currentPage = 1;
            $scope.filterstaffs = $filter('filter')($scope.allStaffs, $scope.query);

            $scope.$watchGroup(['currentPage', 'query'], function(){
                $scope.filterstaffs = $filter('filter')($scope.allStaffs, $scope.query);
                var begin = ($scope.currentPage - 1) * $scope.itemsPerPage;
                var end = begin + $scope.itemsPerPage;
                $scope.paged = {
                    staffs: $scope.filterstaffs.slice(begin, end)
                };
            });
        });

        //判断是否在在选中
        $scope.isInSelected = function(staff){
            for (var i = 0; i < $scope.selectedStaffs.length; i++){
                if ($scope.selectedStaffs[i]['id'] == staff.id) {
                    return true;
                }
            }
            return false;
        };

        //添加评论人
        $scope.addStaff = function(staff){
            if($scope.isInSelected(staff)){
                $scope.deleteStaff(staff);
            } else {
                $scope.selectedStaffs.push(staff);
                $scope.newAddStaffs.push(staff);
            }
        };

        //删除评论人
        $scope.deleteStaff = function (staff) {
            angular.forEach($scope.selectedStaffs, function(employee, n){
                if (employee.id == staff.id){
                    $scope.selectedStaffs.splice(n,1);
                }
            });
            angular.forEach($scope.newAddStaffs, function(employee, n){
                if (employee.id == staff.id){
                    $scope.newAddStaffs.splice(n,1);
                }
            });
        };

        $scope.ok = function(){
            var tempIds = [];
            angular.forEach($scope.newAddStaffs, function(rater){
                tempIds.push(rater.id);
            });
            AdminManage.addNewRaters({'newAddIds': tempIds, 'team': team}).then(function(data){
                $uibModalInstance.close(tempIds);
                tempIds = [];
            });
        };

        //取消
        $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };
    }
]);

controllers.controller("AssessmentManageController", ["$scope", "AdminManage",
    function($scope, AdminManage){
        //获取所有的assessment
        AdminManage.getAllAssessmentsDetail().then(function(data){
            $scope.assessments = data;
        });
        
        //改变是否成为完成状态
        $scope.changeCompleted = function(id, value){
            AdminManage.changeAssessmentCompleted(id, value).then(function(data){
                AdminManage.getAllAssessmentsDetail().then(function(data){
                    $scope.assessments = data;
                });
            });
        };

        //删除assessment
        $scope.deleteAssessment = function(id){
            AdminManage.deleteAssessment(id).then(function(data){
                AdminManage.getAllAssessmentsDetail().then(function(data){
                    $scope.assessments = data;
                });
            })
        }
    }
]);