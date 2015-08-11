﻿angular
    .module('incluso.programa.dashboard', [])
    .controller('programaDashboardController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
        '$timeout',
        '$rootScope',
        '$http',
        '$modal',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http, $modal) {

            _httpFactory = $http;
                    
            $scope.Math = window.Math;

            console.log('loading user');
            $scope.user = JSON.parse(moodleFactory.Services.GetCacheObject("profile"));
            console.log('loading usercourse');
            $scope.usercourse = JSON.parse(moodleFactory.Services.GetCacheObject("usercourse"));
            console.log('loading course');
            $scope.course = JSON.parse(moodleFactory.Services.GetCacheObject("course"));
            console.log('loading currentStage');
            $scope.currentStage = JSON.parse(moodleFactory.Services.GetCacheObject("currentStage"));
            console.log('loading stage');
            
            try {
                if(moodleFactory.Services.GetCacheObject("stage")){
                    $scope.stage = JSON.parse(moodleFactory.Services.GetCacheObject("stage"));                
                }else{                
                    $scope.stage = {};
                }
            }
            catch (e) {
                console.log(e);
            }
            getDataAsync();

            $scope.logout = function(){
                logout($http, $scope, $location);
            };

            $scope.navigateToStage = function(){
                localStorage.setItem("firstTimeStage",0);
                $scope.openModal();
                $location.path('/ProgramaDashboardEtapa/' + $scope.stage.id);
            };
            
             $scope.playWelcome = function(){                 
                 var videoAddress = "assets/media";
                 var videoName = "480x270.mp4";
                cordova.exec(Success, Failure, "CallToAndroid", "PlayLocalVideo", [videoAddress,videoName]);
            };
            
            function Success() {
                
            }
            
            function Failure() {
                
            }
            

            //$scope.navigateTo = function(url){
              //  $location.path(url);
            //};

            function getDataAsync() {
                moodleFactory.Services.GetAsyncUserCourse(_getItem("userId"), getDataAsyncCallback, errorCallback);
            }

            function getDataAsyncCallback(){
                $scope.usercourse = JSON.parse(localStorage.getItem("usercourse"));

                moodleFactory.Services.GetAsyncCourse($scope.usercourse.courseId, function(){
                    $scope.course = JSON.parse(localStorage.getItem("course"));
                    $scope.currentStage = getCurrentStage();                
                    localStorage.setItem("currentStage", $scope.currentStage);
                }, errorCallback);
            }

            function errorCallback(data){
                console.log(data);
            }
                            

            function getCurrentStage(){                
                var currentStage = 1;                                            
                
                for(var i = 0; i < $scope.usercourse.stages.length; i++){                    
                    var uc = $scope.usercourse.stages[i];
                    
                    localStorage.setItem("stage", JSON.stringify(uc));
                    $scope.stage = uc;
                    var firstTimeStage = localStorage.getItem("firstTimeStage");                    
                    if (firstTimeStage == 0) {
                        $scope.stage.firstTime = 0;
                    }                        
                    
                    if(uc.stageStatus === 0){
                        break;
                    }

                    currentStage++;
                }

                return currentStage;
            }

            /* open terms and conditions modal */
            $scope.openModal = function (size) {
                var modalInstance = $modal.open({
                    animation: $scope.animationsEnabled,
                    templateUrl: 'tutorialModal.html',
                    controller: 'tutorialController',
                    size: size
                });
                console.log("modal open");
            };            

        }])
        .controller('tutorialController', function ($scope, $modalInstance) {
            $scope.cancel = function () {
                $modalInstance.dismiss('cancel');
            };
        });
