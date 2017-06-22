var controllers = angular.module('controllers', []);

controllers.controller('KpiManageController', ['$scope', '$http','$uibModal','$location','Kpi',
    function($scope,$http,$uibModal,$location,Kpi){
    //创建新的assessment
    $scope.createNewAssessment = function(){
        var modalInstance = $uibModal.open({
            templateUrl: 'views/selectDate.html',
            controller: 'selectDateController',
            size: 'md',
            backdrop: 'static'
        });
        modalInstance.result.then(function(result){
            $scope.data = result;
            if($scope.data){
                if($scope.data['id']){
                    // console.log('haha');
                    $location.url("/edit_assessment/" + $scope.data['id']);
                }
            }
        });
    };

    //获取所有的assessment
    Kpi.getAllAssessments().then(function(data){
        $scope.allAssessments = data;
    });

        //改变assessment状态为已完成
        $scope.changeAssessmentToCompleted = function(assessmentId){
            var realchange = confirm("您是否要将改考核改为已完成？一旦改变为已完成，则考核不能编辑和打分！")
            if(realchange){
                Kpi.changeAssessmentStatus(assessmentId).then(function(data){
                    Kpi.getAllAssessments().then(function(data){
                        $scope.allAssessments = data;
                    });
                });
            }
        };
}]);

controllers.controller('selectDateController', ['$scope',  '$uibModalInstance', 'Kpi', '$location',
    function($scope, $uibModalInstance, Kpi, $location){
        // $scope.kpiDate = {};

        $scope.open = function ($event) {
            $event.preventDefault();
            $event.stopPropagation();
            $scope.opened = true;
        };

        $scope.options = {
            datepickerMode: "'month'",
            minMode: "month"
        };

        $scope.submit = function () {
            console.log($scope.kpiDate);
            $scope.year = $scope.kpiDate.getFullYear();
            $scope.month = $scope.kpiDate.getMonth() + 1;
            $scope.date = {};
            $scope.date.year = $scope.year;
            $scope.date.month = $scope.month;
            if($scope.month < 10){
                $scope.month = "0" + $scope.month;
            }
            $scope.selectedDate = $scope.year + '-' + $scope.month;
            // console.log($scope.selectedDate);
            Kpi.createAssessment($scope.date).then(function(data){
                console.log(data);
                if(data['error']){
                    alert(data['error']);
                }
                if(data['id']){
                    $uibModalInstance.close(data);
                }

            }, function(data){

            });
        };

        $scope.cancel = function(){
            $uibModalInstance.dismiss('cancel');
        };
    }]);
