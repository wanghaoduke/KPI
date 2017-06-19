var routeApp = angular.module('showScoreRoute',['ngRoute']);

routeApp.config(['$routeProvider', function($routeProvider){
    $routeProvider
        .when('/', {
            templateUrl: '/js/showScore/views/index.html',
            controller: 'ShowScoresController'
        })
        .otherwise({
            redirectTo: '/'
        });
}]);