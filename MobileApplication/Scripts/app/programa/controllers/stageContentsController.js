angular
    .module('incluso.stage.contentscontroller', [])
    .controller('stageContentsController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
        '$timeout',
        '$rootScope',
        '$http',
        '$anchorScroll',
        '$modal',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http, $anchorScroll, $modal) {

            $rootScope.pageName = "Estación: Conócete"
            $rootScope.navbarBlue = true;
            $rootScope.showToolbar = true;
            $rootScope.showFooter = true; 
            $rootScope.showFooterRocks = false; 
            $scope.usercourse = JSON.parse(moodleFactory.Services.GetCacheObject("usercourse"));

            $scope.scrollToTop();
            $scope.$emit('HidePreloader'); //hide preloader

            $scope.activities= 
                                [{id: 12,
                                  name: "File 1",
                                  description: "Lorem ipsum",
                                  activityType: "file",
                                  mandatory: true,
                                  stars: 200,
                                  file: {
                                        id: 3,
                                        filename: "Archivo 1.txt",
                                        path: "assets/images/avatar.svg"
                                      }
                                  },
                                 {id:3,
                                    name:"Contenido ejemplo",
                                    description:null,
                                    activityType:"page",
                                    mandatory: false,
                                    stars:null,
                                    pageContent:"\u003Cp\u003E\u003Cbr\u003EContenido ejemplo\u003C\/p\u003E"},
                                 {id: 12,
                                  name: "URL 1",
                                  description: "Lorem ipsum",
                                  activityType: "url",
                                  isVideo: false,
                                  mandatory: false,
                                  stars: 200,
                                  url: "www.google.com"},
                                  {id:7,
                                  name:"Mira  hasta donde eres capaz de llegar \u00a1Los l\u00edmites los pones t\u00fa!",
                                  description:"video",
                                  activityType:"url",
                                  isVideo: true,
                                  mandatory: true,
                                  stars:null,
                                  url:"https:\/\/www.youtube.com\/embed\/watch?v=ocrjltwc_Fs"}
                                 ];

             for (var i=0; i<$scope.activities.length; i++) {
              if ($scope.activities[i].isVideo) {
                $scope.activities[i].activityType = "video";                
              }
            }

            function getDataAsync() {
                moodleFactory.Services.GetAsyncActivities($routeParams.moodleid, getActivityInfoCallback);
            }

            function getActivityInfoCallback() {
                $scope.activities = JSON.parse(moodleFactory.Services.GetCacheObject("activity/" + $routeParams.moodleid));
            }

            //getDataAsync();

            $scope.back = function () {
                $location.path('/ProgramaDashboard');
            }

        }]);