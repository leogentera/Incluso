// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.public.login', ['ui.select2', 'ngTagsInput'])
    .controller('publicLoginController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http) {

            $scope.login = function (username, password) {
                var username = $("[name='username']").val();
                $location.path('/ProgramaDashboard');
            }

            $scope.loginWithFacebook = function () {
                $location.path('/ProgramaDashboard');
            }

        }]);