var routeApp = angular.module('advicesRoute', ['ngRoute']);

routeApp.config(['$routeProvider',function($routeProvider){
    $routeProvider
        .when('/suggester/index', {
            templateUrl: 'js/advices/views/index.html',
            controller: 'AdvicesController'
        })
        .when('/rater/index', {
            templateUrl: 'js/advices/views/raterIndex.html',
            controller: 'RaterAdvicesController'
        })
        .when('/advice_show/:id', {
            templateUrl: 'js/advices/views/adviceShow.html',
            controller: 'AdviceEditController'
        })
        .when('/advice_edit/:id', {
            templateUrl: 'js/advices/views/adviceEdit.html',
            controller: 'AdviceEditController'
        })
        .when('/rater_edit/:id', {
            templateUrl: 'js/advices/views/raterEdit.html',
            controller: 'RaterEditController'
        })
        .otherwise({
            redirectTo: '/suggester/index'
        });
}]);