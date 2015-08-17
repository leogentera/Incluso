angular
    .module('incluso.programa.acercaPrograma', [])
    .controller('programaAcercaProgramaController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
        '$timeout',
        '$rootScope',
        '$http',
        '$modal',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http, $modal) {
            $rootScope.pageName = "Mision incluso"
            $rootScope.navbarBlue = false;
            $rootScope.showToolbar = true;
            $rootScope.showFooter = true;  
            $scope.back = function () {
                $location.path('/ProgramaDashboard');
            }

        }]);
