var resService = angular.module('resService', []);
resService.factory("Advices", ['$http', '$q',
    function($http, $q){
        var advices = {};
        
        //保存新提的建议
        advices.saveTheAdvice = function(data){
            return $q(function(resolve, reject){
                $http({
                    method: "POST",
                    url: '/advices',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: $.param(data)
                })
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //获取所有的合理化建议
        advices.getAllAdvices = function(){
            return $q(function(resolve, reject){
                $http.get('/advices/get_all_auth_advices')
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //获取该id的建议详情
        advices.getAdviceDetail = function(id){
            return $q(function(resolve, reject){
                $http.get('/advices/' + id)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //获取评审的advice内容
        advices.raterGetAdviceDetail = function(id){
            return $q(function(resolve, reject){
                $http.get('/rater_edit/get_advice_detail/' + id)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };


        //获得所有的建议者的advice
        advices.getAllSuggesterAllAdvices = function(team, search){
            return $q(function(resolve, reject){
                $http.get('/advices/rater/get_all_suggester_all_advices?team=' + team + "&search=" + search)
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //编辑该id的建议详情
        advices.updateAdvice = function(id, data){
            return $q(function(resolve, reject){
                $http({
                    method: "PUT",
                    url: '/advices/' + id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: $.param(data)
                })
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };

        //保存rater对advice的评论
        advices.saveTheRaterJudgeAdvice = function(id, data){
            return $q(function(resolve, reject){
                $http({
                    method: "POST",
                    url: '/rater_edit/rater_judge_advice/' + id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: $.param(data)
                })
                    .success(function(data){
                        resolve(data);
                    }).error(function(data){
                    reject(data);
                });
            });
        };
        
        return advices;
    }
]);