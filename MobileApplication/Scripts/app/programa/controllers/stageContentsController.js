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

            $scope.scrollToTop();
            $scope.$emit('HidePreloader'); //hide preloader

            $scope.activities = null;
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