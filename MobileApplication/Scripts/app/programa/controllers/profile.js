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

            $scope.currentPage = 1;
            $scope.model = loadDataAsync();

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
            };

            $scope.edit = function() {
                $location.path('/Perfil/Editar');
            }

            $scope.index = function() {
                $location.path('/Perfil');
            }

            $scope.navigateToDashboard = function(){
                $location.path('/ProgramaDashboard');
            }

            $scope.save = function(){
                var jMdoel = JSON.stringify($scope.model);
                localStorage.setItem("profile", jMdoel);

                $scope.index();
            }

            function loadDataAsync(){
                return JSON.parse(localStorage.getItem("profile"));
            }

            $scope.addStudy = function(){
                $scope.model.studies.push({});
            }
            $scope.deleteStudy = function(index){
                $scope.model.studies.splice(index, 1);
            }

            $scope.addPhone = function(){
                $scope.model.phones.push(new String());
            }
            $scope.deletePhone = function(index){
                $scope.model.phones.splice(index, 1);
            }

            $scope.addEmail = function(){
            }
            $scope.deleteEmail = function(index){
            }

            $scope.addSocialNetwork = function(){
                $scope.model.socialNetworks.push({});
            }
            $scope.deleteSocialNetwork = function(index){
                $scope.model.socialNetworks.splice(index, 1);
            }

            $scope.addFamiliaCompartamos = function(){
                $scope.model.familiaCompartamos.push({});
            }
            $scope.deleteFamiliaCompartamosk = function(index){
                $scope.model.familiaCompartamos.splice(index, 1);
            }
        }]);
