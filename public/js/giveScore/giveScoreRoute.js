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
