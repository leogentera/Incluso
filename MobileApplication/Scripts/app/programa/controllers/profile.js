// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.programa.profile', [])
    .controller('programaProfileController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http) {
            
            /* Helpers */
            $scope.currentPage = 1;

            $scope.model = JSON.parse(localStorage.getItem("profile"));

            $scope.hasDreams = function(){
                var dreamsToBe = $scope.dreamsToBe || [];
                var dreamsToHave = $scope.dreamsToHave || [];
                var dreamsToDo = $scope.dreamsToDo || [];

                return dreamsToBe.length > 0 || dreamsToHave.length > 0 || dreamsToDo.length > 0;
            };

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
            };

            $scope.edit = function() {
                $location.path('/Perfil/Editar');
            }

            $scope.login = function() {
                $location.path('/Perfil');
            }


        }]);
