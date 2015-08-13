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

            /* Helpers */
            $scope.currentPage = 1;
            $scope.loading = false;

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
                var name="{\"UserId\": 103,\"Alias\": \"Laura Giaccone\"\"PathImagen\": \"Android/data/<app-id>/images\",\"Color Cabello\": \"\",\"Estilo Cabello\": \"\",\"Traje color principal\": \"\",\"Traje color secundario\": \"\",\"Rostro\": \"\",\"Color de piel\": \"\",\"Imagen Recortada\": \"\"}";
                cordova.exec(SuccessAvatar, FailureAvatar, "CallToAndroid", "openApp", [name]);
            };
            
              function Success() {
                
            }
            
            function Failure() {
                
            }
            
            function SuccessAvatar() {
                 $timeout($location.path('/ProgramaDashboard'), 10);
                
            }
            
            function FailureAvatar() {
            }

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
                $scope.scrollToTop();
            };
        }]);
