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
                                  activityType: "File",
                                  stars: 200,
                              file: {
                                    id: 3,
                                    filename: "Archivo 1.txt",
                                    path: "assets/images/avatar.svg"
                                  }},
                                 {id: 12,
                                  name: "Page 1",
                                  description: "Lorem ipsum",
                                  activityType: "Page",
                                  stars: 200,
                                  pageContent: "Bienvenido vdsafn kdsanf nsakdfn jknaskfjdas"},
                                 {id: 12,
                                  name: "URL 1",
                                  description: "Lorem ipsum",
                                  activityType: "URL",
                                  stars: 200,
                                  url: "www.google.com"}
                                 ];

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