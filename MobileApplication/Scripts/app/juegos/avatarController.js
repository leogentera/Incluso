// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.juegos.avatar', [])
    .controller('juegosAvatarController', [
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

            $scope.scrollToTop();
            $scope.avatarInfo = moodleFactory.Services.GetCacheJson("avatarInfo");
            $scope.imageSrc =  $scope.avatarInfo["Color Cabello"];

            $scope.selectAvatarCabelloAmarillo = function() {
                $scope.avatarInfo["Color Cabello"] = "amarillo";
                localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));
                $location.path('/ProgramaDashboard');
            }
            
            $scope.selectAvatarCabelloRojo = function() {
                $scope.avatarInfo["Color Cabello"] = "rojo";
                localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));
                $location.path('/ProgramaDashboard');
            }


            $scope.selectAvatarCabelloVerde = function() {
                $scope.avatarInfo["Color Cabello"] = "verde";
                localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));
                $location.path('/ProgramaDashboard');
            }


            $scope.selectAvatarCabelloCafe= function() {
                $scope.avatarInfo["Color Cabello"] = "cafe";
                localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));
                $location.path('/ProgramaDashboard');
            }
            
        }]);
