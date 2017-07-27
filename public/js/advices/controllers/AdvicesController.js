var controllers = angular.module('controllers', []);

controllers.controller('AdvicesController',['$scope', "Advices", "$uibModal",
    function($scope, Advices, $uibModal){
        $scope.advice = {};
        //获取已经有的建议
        Advices.getAllAdvices().then(function(data){
            $scope.formDatas = data;
            for(var i = 0; i < $scope.formDatas.length; i++){
                if($scope.formDatas[i]['title'].length > 10){
                    $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['title'].substr(0, 8);
                    $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['showTitle'] + '...';
                }else{
                    $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['title'];
                }
                if($scope.formDatas[i]['content'].length > 20){
                    $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['content'].substr(0, 17);
                    $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['showContent'] + '...';
                }else{
                    $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['content'];
                }
            }
            $scope.currentPage = 1;
            $scope.itemsPerPage = 10;
            $scope.pageDatas = {};
            $scope.$watch('currentPage', function(){
                var begin = ($scope.currentPage - 1) * $scope.itemsPerPage;
                var end = begin + $scope.itemsPerPage;
                $scope.paged = {
                    detail: $scope.formDatas.slice(begin, end)
                };
            });
        });

        
        //保存建议呀
        $scope.isSaving = false;
        $scope.saveAdvice = function(){
            if(!$scope.isSaving){
                $scope.isSaving = true;
                Advices.saveTheAdvice($scope.advice).then(function(data){
                    alert("已经保存成功！");
                    $scope.isSaving = false;
                    Advices.getAllAdvices().then(function(data){
                        $scope.formDatas = data;
                        $scope.advice = {};
                        for(var i = 0; i < $scope.formDatas.length; i++){
                            if($scope.formDatas[i]['title'].length > 10){
                                $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['title'].substr(0, 8);
                                $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['showTitle'] + '...';
                            }else{
                                $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['title'];
                            }
                            if($scope.formDatas[i]['content'].length > 20){
                                $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['content'].substr(0, 17);
                                $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['showContent'] + '...';
                            }else{
                                $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['content'];
                            }
                        }
                        $scope.currentPage = 1;
                        $scope.paged = {
                            detail: $scope.formDatas.slice(0, 10)
                        };
                    });
                },function(data){
                    $scope.isSaving = false;
                    if(data){
                        if(data['data']){
                            angular.forEach(data['data'], function(data){
                                alert(data);
                            })
                        }
                    }
                });
            }
        };
        
        
        //点击详情后弹出显示框
        $scope.showDetail = function(comment){
            var modalInstance = $uibModal.open({
                templateUrl: 'views/showRaterComment.html',
                controller: 'ShowRaterCommentController',
                size: 'sm',
                // backdrop: 'static'
                resolve: {
                    adviceComment: function(){
                        return comment;
                    }
                }
            });
        }
        
    }
]);

controllers.controller("ShowRaterCommentController", ["$scope", "$uibModalInstance", "Advices", "adviceComment",
    function($scope, $uibModalInstance, Advices, adviceComment){
        $scope.comment = adviceComment;
    }
]);

controllers.controller("AdviceEditController", ['$scope', 'Advices', '$routeParams', '$location',
    function($scope, Advices, $routeParams, $location){

        //获取该id的advice
        Advices.getAdviceDetail($routeParams['id']).then(function(data){
            $scope.advice = data;
        });

        //更新advice的内容
        $scope.updateAdvice = function(){
            Advices.updateAdvice($routeParams['id'], $scope.advice).then(function(data){
                $location.url("/advice_show/" + $routeParams['id']);
            }, function(data){
                if(data){
                    if(data['data']){
                        angular.forEach(data['data'], function(data){
                            alert(data);
                        })
                    }
                }
            });
        }
    }
]);

controllers.controller("RaterAdvicesController", ['$scope', 'Advices', '$uibModal',
    function($scope, Advices, $uibModal){
        $scope.team = "all";
        $scope.search = "";
        $scope.showClear = false;

        function getAllData (){
            Advices.getAllSuggesterAllAdvices($scope.team, $scope.search).then(function(data){
                $scope.formDatas = data;
                for(var i = 0; i < $scope.formDatas.length; i++){
                    if($scope.formDatas[i]['title'].length > 10){
                        $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['title'].substr(0, 8);
                        $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['showTitle'] + '...';
                    }else{
                        $scope.formDatas[i]['showTitle'] = $scope.formDatas[i]['title'];
                    }
                    if($scope.formDatas[i]['content'].length > 20){
                        $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['content'].substr(0, 17);
                        $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['showContent'] + '...';
                    }else{
                        $scope.formDatas[i]['showContent'] = $scope.formDatas[i]['content'];
                    }
                }
                $scope.currentPage = 1;
                $scope.itemsPerPage = 15;
                $scope.$watch('currentPage', function(){
                    var begin = ($scope.currentPage - 1) * $scope.itemsPerPage;
                    var end = begin + $scope.itemsPerPage;
                    $scope.paged = {
                        detail: $scope.formDatas.slice(begin, end)
                    };
                    $(document).ready(function(){
                        $("[data-toggle='popover']").popover();
                    });
                });
            });
        }

        //获取所有的advice内容
        getAllData();

        //清除input内的搜索内容
        $scope.clearInput = function(){
            $scope.search = "";
            $scope.showClear = false;
            getAllData();
        };

        //什么时候显示清除按钮
        $scope.$watch("search", function(){
            if($scope.search.length > 0){
                $scope.showClear = true;
            }else{
                $scope.showClear = false;
            }
        });

        //搜索框内内容
        $scope.searchInput = function(){
            getAllData();
        };

        //变换team
        $scope.changeTeam = function(team){
            $scope.team = team;
            getAllData();
        };


        $scope.$watch('currentPage', function(){
            // console.log('sss');
            // console.log($scope.currentPage);
            if($scope.currentPage){
                var begin = ($scope.currentPage - 1) * $scope.itemsPerPage;
                var end = begin + $scope.itemsPerPage;
                $scope.paged = {
                    detail: $scope.formDatas.slice(begin, end)
                };
            }
        });
        
        //点击建议内容后显示弹出框
        $scope.showContentDetail = function(content){
            var modalInstance = $uibModal.open({
                templateUrl: 'views/showSuggesterContent.html',
                controller: 'ShowSuggesterContentController',
                size: 'md',
                // backdrop: 'static'
                resolve: {
                    adviceContent: function(){
                        return content;
                    }
                }
            });
        }

    }
]);

controllers.controller("ShowSuggesterContentController", ["$scope", "$uibModalInstance", "Advices", "adviceContent",
    function($scope, $uibModalInstance, Advices, adviceContent){
        $scope.adviceContent = adviceContent;
    }
]);

controllers.controller("RaterEditController", ["$scope", "Advices", "$routeParams", '$location',
    function($scope, Advices, $routeParams, $location){
        $scope.isAdopt = false;
        $scope.starArray = [false, false, false, false, false];
        $scope.rater = {};
        //获取advice的内容
        Advices.raterGetAdviceDetail($routeParams['id']).then(function(data){
            $scope.adviceDetail = data[0];
            if($scope.adviceDetail['is_accept'] == 1){
                $scope.isAdopt = $scope.adviceDetail['is_accept'];
                $scope.changeScore($scope.adviceDetail['score']);
                $scope.rater.comment = $scope.adviceDetail['comment'];
            }
        });

        //修改分数的五角星
        $scope.changeScore = function(count){
            count = parseInt(count);
            $scope.raterScore = count;
            for(var i = 0; i < $scope.starArray.length; i++){
                if(i < parseInt(count)){
                    $scope.starArray[i] = true;
                }
                if(i >= parseInt(count)){
                    $scope.starArray[i] = false;
                }
            }
        };

        //更改是否采纳
        $scope.changeIsAdopt = function(value){
            if(value == 1){
                $scope.isAdopt = true;
            }
            if(value == 0){
                $scope.isAdopt = false;
            }
        };

        //保存评论呀
        $scope.saveRaterEdit = function(id){
            $scope.temdata = {};
            $scope.temdata['is_processed'] = 1;
            $scope.temdata['score'] = $scope.raterScore;
            $scope.temdata['comment'] = $scope.rater.comment;
            if($scope.isAdopt){
                $scope.temdata['is_accept'] = 1;
                if(!$scope.temdata['score']){
                    alert("如果接受建议，必须给分数！");
                }
            }else{
                $scope.temdata['is_accept'] = 0;
            }
            Advices.saveTheRaterJudgeAdvice(id, $scope.temdata).then(function(data){
                $location.url("/rater/index");
            });
        };
    }
]);