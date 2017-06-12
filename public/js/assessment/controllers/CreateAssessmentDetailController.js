var assessmentControllers = angular.module('assessmentControllers',[]);

assessmentControllers.controller('CreateAssessmentDetailController',['$scope', '$http', '$location', '$routeParams', 'Kpi', '$uibModal',
    function($scope, $http, $location, $routeParams, Kpi, $uibModal){
        $scope.assessmentDetail = {};
        // $scope.title1 = '考核管理';
        // $scope.title2 = '编辑考核';
        // $scope.titleLink1 = '#/';
        // $scope.titleLink2 = '#/';

        //获取全部被评信息
        Kpi.getStaffDetail($routeParams['id']).then(function(data){
            $scope.assessmentDetail['plan_staffs'] = data[0];
            $scope.assessmentDetail['development_staffs'] = data[1];
            $scope.assessmentDetail['raters'] = data[2];
            $scope.assessmentDetail['assessment'] = data[3];
        }, function(data){});

        //添加评选人
        $scope.addRater = function(staffId){
            console.log(staffId);
            Kpi.getRaters({'staff_id': staffId, 'assessment_id': $routeParams['id']}).then(function(data){
                // console.log(data.length);
                // console.log('data:'+data);
                if(data.length > 0){
                    $scope.selectedRaters = data;
                    // console.log('sss:'+$scope.selectedRaters);
                }else{
                    $scope.selectedRaters = [];
                }
                var modalInstance = $uibModal.open({
                    templateUrl: 'views/addRaters.html',
                    controller: 'AddRaterController',
                    size: 'lg',
                    resolve: {
                        selectedRaters: function () {
                            return $scope.selectedRaters;
                        },
                        assessmentId: function () {
                            return $routeParams['id'];
                        },
                        staffId: function(){
                            return staffId;
                        }
                    }
                });
                modalInstance.result.then(function (result){
                    Kpi.getStaffDetail($routeParams['id']).then(function(data){
                        $scope.assessmentDetail['plan_staffs'] = data[0];
                        $scope.assessmentDetail['development_staffs'] = data[1];
                        $scope.assessmentDetail['raters'] = data[2];
                        $scope.assessmentDetail['assessment'] = data[3];
                    }, function(data){});
                });
            }, function(data){});
        };

        $scope.ok = function(){
            console.log($scope.assessmentDetail['id']);
        };
    }]
);

assessmentControllers.controller('AddRaterController', ['$scope',  '$uibModalInstance', 'Kpi', '$location', 'selectedRaters', '$filter', 'assessmentId', 'staffId',
    function($scope, $uibModalInstance, Kpi, $location, selectedRaters, $filter, assessmentId,staffId){
        $scope.selectedStaffIds = [];
        $scope.selectedStaffs = [];
        Kpi.getAllStaffs().then(function(data){
            $scope.allStaffs = [];   //备选所有员工
            data.forEach(function(staff){
                var tempStaff = {};
                tempStaff.id = staff.id;
                tempStaff.name = staff.name;
                tempStaff.department = staff.department;
                $scope.allStaffs.push(tempStaff);
            });

            if(selectedRaters){
                angular.forEach(selectedRaters, function(rater){
                    $scope.selectedStaffIds.push(rater.id);
                });
                $scope.selectedStaffs = angular.copy(selectedRaters);
                //获取被选中rater的详情
                Kpi.getSelectedStaffDetails(assessmentId ,{'selectedStaffIds': $scope.SelectedStaffIds, 'staffId': staffId}).then(function(data){
                    angular.forEach(data, function(user){
                        angular.forEach($scope.selectedStaffs, function(staff){
                            if(user['id'] == staff['id']){
                                staff['percentage'] = user['percentage'];
                            }
                        });
                    });
                    console.log($scope.selectedStaffs);
                }, function(data){});
            }
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
        }, function(data){});

        $scope.isInselected = function (staff) {
            for (var i = 0; i < $scope.selectedStaffs.length; i++) {
                if ($scope.selectedStaffs[i]['id'] == staff.id) {
                    return true;
                }
            }
            return false;
        };

        //添加评论人
        $scope.addStaff = function (staff) {
            if($scope.isInselected(staff)){
                $scope.deleteStaff(staff);
            } else {
                staff.percentage = null;
                $scope.selectedStaffs.push(staff);
            }
        };

        //删除评论人
        $scope.deleteStaff = function (staff) {
            angular.forEach($scope.selectedStaffs, function(employee, n){
                if (employee.id == staff.id){
                    $scope.selectedStaffs.splice(n,1);
                }
            });
        };

        //提交
        $scope.ok = function(){
            var sumPercentage = 0;
            for(var i = 0; i < $scope.selectedStaffs.length; i++){
                if(!$scope.selectedStaffs[i]['percentage']){
                    $scope.selectedStaffs[i]['percentage'] = 0;
                }
                sumPercentage = sumPercentage + parseInt($scope.selectedStaffs[i]['percentage']);
            }
            if( sumPercentage != 100){
                alert('所有百分比的和应该是100%！')
            }else{
                Kpi.editRaters(assessmentId, {'staffId': staffId, 'selectedStaffs': $scope.selectedStaffs}).then(function(data){
                    $uibModalInstance.close($scope.selectedStaffs);
                })
            }
            // console.log($scope.selectedStaffs);
        };

        //取消
        $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };

    }]);