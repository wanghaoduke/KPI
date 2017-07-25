var routeApp = angular.module('giveScoreRoute',['ngRoute']);

routeApp.config(['$routeProvider',function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: '/js/giveScore/views/index.html',
            controller: 'showAssessmentController'
        })
        .when('/give_score/:id', {
            templateUrl: '/js/giveScore/views/giveScore.html',
            controller: 'giveScoreController'
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
            case 5:
                return '产品总监组';
                break;
        }
    }
});
