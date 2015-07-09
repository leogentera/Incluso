// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.public.recoverPassword', ['ui.select2', 'ngTagsInput'])
    .controller('publicRecoverPasswordController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http) {
            
            $scope.currentPage = 1;



            $scope.recover = function() {
                $location.path('/');
            }

            $scope.cancel = function() {
                $location.path('/');
            }

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
            };


        }]);


