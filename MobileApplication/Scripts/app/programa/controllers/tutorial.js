// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.programa.tutorial', [])
    .controller('programaTutorialController', [
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

            /* Models */
            $scope.hasSeenTutorial = moodleFactory.Services.GetCacheObject("HasSeenTutorial");
            $scope.avatarInfo = moodleFactory.Services.GetCacheJson("avatarInfo");
            $scope.user = moodleFactory.Services.GetCacheJson("profile");

            /* Helpers */
            $scope.currentPage = 1;
            $scope.loading = false;

            if ($scope.avatarInfo == null) {
                $scope.avatarInfo = {
                    "UserId": $scope.user.UserId,
                    "Alias": $scope.user.username,
                    "Aplicacion": "Mi Avatar",
                    "Estrellas": $scope.user.stars,
                    "PathImagen": "Android/data/<app-id>/images",
                    "Color Cabello": "amarillo",
                    "Estilo Cabello": "",
                    "Traje color principal": "",
                    "Traje color secundario": "",
                    "Rostro": "",
                    "Color de piel": "",
                    "Escudo:": "",
                    "Imagen Recortada": "",
                };             
            }

            //if ($scope.hasSeenTutorial && $scope.hasSeenTutorial == "true")
            //{
                //$location.path('/ProgramaDashboard');
            //}

            $scope.continue = function() {
				$scope.hasSeenTutorial = true;
                localStorage.setItem("HasSeenTutorial", "true");
                $scope.scrollToTop();
                $location.path('/Perfil');

            }
            
              $scope.playVideo = function(){                 
                 var videoAddress = "assets/media";
                 var videoName = "TutorialTest2.mp4";
                cordova.exec(Success, Failure, "CallToAndroid", "PlayLocalVideo", [videoAddress,videoName]);
            };
            
            $scope.avatar = function(){
                $scope.avatarInfo.UserId = $scope.user.UserId;
                $scope.avatarInfo.Alias = $scope.user.username;
                $scope.avatarInfo.Estrellas = $scope.user.stars;
                localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));

                $scope.scrollToTop();         
                $location.path('/Juegos/Avatar');

                //the next lines are related to the actual java integatration
                //$location.path('/ProgramaDashboard');
                //cordova.exec(SuccessAvatar, FailureAvatar, "CallToAndroid", "openApp",[JSON.stringify($scope.avatarInfo)]);


            };
            
              function Success() {
                
            }
            
            function Failure() {
                
            }
            
            function SuccessAvatar(data) {
                  localStorage.setItem("avatarInfo", JSON.stringify(data));
            }
            
            function FailureAvatar() {
            }

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
                $scope.scrollToTop();
            };
        }]);
