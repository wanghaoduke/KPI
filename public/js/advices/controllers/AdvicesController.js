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
                for(var i = 0; i < $scope.paged['detail'].length; i++){
                    $scope.paged['detail'][i]['showTips'] = false;
                }
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
                        for(var i = 0; i < $scope.paged['detail'].length; i++){
                            $scope.paged['detail'][i]['showTips'] = false;
                        }
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
        // $scope.showDetail = function(comment){
        //     var modalInstance = $uibModal.open({
        //         templateUrl: 'views/showRaterComment.html',
        //         controller: 'ShowRaterCommentController',
        //         size: 'sm',
        //         // backdrop: 'static'
        //         resolve: {
        //             adviceComment: function(){
        //                 return comment;
        //             }
        //         }
        //     });
        // }
        
        $scope.is_change_show = true;   //a标签失去焦点后 是否改变显示状态
        $scope.mouseOverA = function(id, value){
            $scope.advice_tempId = id;
            $scope.advice_tempValue = value;
        };
        $scope.mouseLeaveA = function(){
            $scope.advice_tempId = null;
            $scope.advice_tempValue = null;
        };
        $scope.changeShowTips = function(id){   //点击a标签后事件
            if(id == $scope.advice_tempId){
                for(var i = 0; i < $scope.paged['detail'].length; i++){
                    if(id == $scope.paged['detail'][i]['id']){
                        $scope.paged['detail'][i]['showTips'] = !$scope.advice_tempValue;
                        $scope.advice_tempValue = $scope.paged['detail'][i]['showTips'];
                    }else{
                        $scope.paged['detail'][i]['showTips'] = false;
                    }
                }
            }
        };
        
        $scope.lostBlurA = function(){
            if($scope.is_change_show){
                for(var i = 0; i < $scope.paged['detail'].length; i++){
                    $scope.paged['detail'][i]['showTips'] = false;
                }
            }
        };

        $scope.mouseOverDiv = function(){
            $scope.is_change_show = false;
        };

        $scope.mouseLeaveDiv = function(){
            $scope.is_change_show = true;
        };
        
        $scope.lostBlurDiv = function(){
            for(var i = 0; i < $scope.paged['detail'].length; i++){
                $scope.paged['detail'][i]['showTips'] = false;
            }
        };
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
                    // $scope.formDatas[i]['showTips'] = false;
                }

                $scope.currentPage = 1;
                $scope.itemsPerPage = 15;

                $scope.$watch('currentPage', function(){
                    var begin = ($scope.currentPage - 1) * $scope.itemsPerPage;
                    var end = begin + $scope.itemsPerPage;
                    $scope.paged = {
                        detail: $scope.formDatas.slice(begin, end)
                    };
                    for(var i = 0; i < $scope.paged['detail'].length; i++){
                        $scope.paged['detail'][i]['showTips'] = false;
                    }
                });
            });
        }
        // $scope.$watch('currentPage', function(){
        //     // console.log('sss');
        //     // console.log($scope.currentPage);
        //     if($scope.currentPage){
        //         var begin = ($scope.currentPage - 1) * $scope.itemsPerPage;
        //         var end = begin + $scope.itemsPerPage;
        //         $scope.paged = {
        //             detail: $scope.formDatas.slice(begin, end)
        //         };
        //     }
        // });

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



        
        //点击建议内容后显示弹出框
        $scope.is_show_stauts = true;
        $scope.showContentDetail = function(id){
            if(id == $scope.tempId){
                for(var i = 0; i < $scope.paged['detail'].length; i++){
                    if(id == $scope.paged['detail'][i]['id']){
                        $scope.paged['detail'][i]['showTips'] = !$scope.tempValue;
                        $scope.tempValue = $scope.paged['detail'][i]['showTips'];
                    }else{
                        $scope.paged['detail'][i]['showTips'] = false;
                    }
                }
            }
        };

        //失去焦点后处理
        $scope.lostBlur = function(){
            if($scope.is_show_stauts){
                for(var i = 0; i < $scope.paged['detail'].length; i++){
                    $scope.paged['detail'][i]['showTips'] = false;
                }
            }
        };

        //鼠标overA标签处理
        $scope.mouseOverA = function(id, value){
            $scope.tempId = id;
            $scope.tempValue = value;
        };

        //鼠标离开A标签处理
        $scope.mouseLeaveA = function(){
            $scope.tempId = null;
            $scope.tempValue = null;
        };


        //鼠标是否在弹出框内
        $scope.getId = function(value){
            $scope.is_show_stauts = value;
        };
        // $scope.showContentDetail = function(content){
        //     var modalInstance = $uibModal.open({
        //         templateUrl: 'views/showSuggesterContent.html',
        //         controller: 'ShowSuggesterContentController',
        //         size: 'md',
        //         // backdrop: 'static'
        //         resolve: {
        //             adviceContent: function(){
        //                 return content;
        //             }
        //         }
        //     });
        // }

    }
]);

// controllers.controller("ShowSuggesterContentController", ["$scope", "$uibModalInstance", "Advices", "adviceContent",
//     function($scope, $uibModalInstance, Advices, adviceContent){
//         $scope.adviceContent = adviceContent;
//     }
// ]);

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