﻿angular
    .module('incluso.programa.foro', [])
    .controller('programaForoController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
        '$timeout',
        '$rootScope',
        '$http',
        '$modal',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http, $modal) {
            $rootScope.showToolbar = false;
            $rootScope.showFooter = true;
            $scope.back = function () {
                $location.path('/ProgramaDashboard');
            }

        }]);
