var routeApp = angular.module('adminRoute',['ngRoute']);

routeApp.config(['$routeProvider',function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: 'js/admin/views/index.html',
            controller: 'AdminIndexController'
        })
        .when('/staff_manage', {
            templateUrl: 'js/admin/views/staffManage.html',
            controller: 'StaffManageController'
        })
        .when('/rater_set', {
            templateUrl: 'js/admin/views/raterSet.html',
            controller: 'RaterSetController'
        })
        .when('/assessment_manage', {
            templateUrl: 'js/admin/views/assessmentManage.html',
            controller: 'AssessmentManageController'
        })
        .otherwise({
            redirectTo: '/'
        });
}]);

routeApp.filter('departmentFilter', function(){
    return function(input){
        switch(parseInt(input)){
            case 1:
                return '产品经理组';
            break;
            case 2:
                return '技术主管组';
            break;
            case 3:
                return '策划组';
            break;
            case 4:
                return '开发组';
            break;
        }
    }
});