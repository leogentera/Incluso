angular
    .module('incluso.programa.dashboard.etapa', [])
    .controller('programaEtapaController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http) {

            $scope.Math = window.Math;

            getDataAsync();

            alert($routeParams.stageId);

            function getDataAsync() {
                $scope.user = JSON.parse(localStorage.getItem("user"));
                $scope.course = JSON.parse(localStorage.getItem("course"));
                $scope.usercourse = JSON.parse(localStorage.getItem("usercourse"));
            }

        }]);
