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
                $('#avatarControl').attr("src", 'assets/images/avatar/create_avatar_cabello_amarillo.jpg');
            }
            
            $scope.selectAvatarCabelloRojo = function() {
                $scope.avatarInfo["Color Cabello"] = "rojo";
                $('#avatarControl').attr("src", 'assets/images/avatar/create_avatar_cabello_rojo.jpg');
            }


            $scope.selectAvatarCabelloVerde = function() {
                $scope.avatarInfo["Color Cabello"] = "verde";
                $('#avatarControl').attr("src", 'assets/images/avatar/create_avatar_cabello_verde.jpg');
            }


            $scope.selectAvatarCabelloCafe= function() {
                $scope.avatarInfo["Color Cabello"] = "cafe";
                $('#avatarControl').attr("src", 'assets/images/avatar/create_avatar_cabello_cafe.jpg');

            }

            $scope.selectAvatar= function() {
                localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));
                $location.path('/ProgramaDashboard');
            }
            
        }]);
