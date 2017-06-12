var routeApp = angular.module('assessmentRoute',['ngRoute']);

routeApp.config(['$routeProvider',function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: 'js/assessment/views/index.html',
            controller: 'KpiManageController'
        })
        .when('/edit_assessment/:id', {
            templateUrl: 'js/assessment/views/editAssessment.html',
            controller: 'CreateAssessmentDetailController'
        })
        .otherwise({
            redirectTo: '/'
        });
}]);

routeApp.filter('departmentFilter', function() {
    return function(input) {
        switch (parseInt(input)) {
            case 1:
                return '产品经理组';
            case 2:
                return '技术主管组';
            case 3:
                return '策划组';
            case 4:
                return '开发组';
        }
    }
});