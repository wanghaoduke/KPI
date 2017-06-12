
var controllers = angular.module('controllers', []);


controllers.controller('KpiManageController', ['$scope', '$http','$uibModal',function($scope,$http,$uibModal){
    
    $scope.createNewAssessment = function(){
        var modalInstance = $uibModal.open({
            templateUrl: 'views/selectDate.html',
            controller: 'selectDateController',
            size: 'md',
            backdrop: 'static'
        });
        modalInstance.result.then(function(result){

        })
    }
}]);

controllers.controller('selectDateController', ['$scope',  '$uibModalInstance', 'Kpi',
    function($scope, $uibModalInstance, Kpi){
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
            if($scope.month < 10){
                $scope.month = "0" + $scope.month;
            }
            $scope.selectedDate = $scope.year + '-' + $scope.month;
            console.log($scope.selectedDate);
            
            Kpi.createAssessment($scope.year, $scope.month).then(function(data){
                $uibModalInstance.close($scope.selectedDate);
            }, function(data){
                
            });
            // $uibModalInstance.close($scope.selectedDate);
        };

        $scope.cancel = function(){
            $uibModalInstance.dismiss('cancel');
        };
}]);
