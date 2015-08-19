// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.stage.gameretomultiplecontroller', [])
    .controller('stageGameRetoMultipleController', [
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

            $scope.downloadGame = function () {
                $location.path('/ZonaDeVuelo/Conocete/RetoMultipleExternalApp');
            }

            $scope.back = function () {
                $location.path('/ProgramaDashboard');
            }

        }]);