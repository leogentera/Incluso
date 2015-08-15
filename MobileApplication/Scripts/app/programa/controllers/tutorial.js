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
                getDataAsync();
            }

            function getDataAsync() {
                moodleFactory.Services.GetAsyncAvatar(_getItem("userId"), getAvatarInfoCallback);
            }

            function getAvatarInfoCallback(){

                $scope.avatarInfo = moodleFactory.Services.GetCacheJson("avatarInfo");

                if ($scope.avatarInfo == null) {
                    $scope.avatarInfo = [{
                        "userid": $scope.user.UserId,
                        "alias": $scope.user.username,
                        "aplicacion": "Mi Avatar",
                        "estrellas": $scope.user.stars,
                        "PathImagen": "Android/data/<app-id>/images",
                        "color_cabello": "amarillo",
                        "estilo_cabello": "",
                        "traje_color_principal": "",
                        "traje_color_secundario": "",
                        "rostro": "",
                        "color_de_piel": "",
                        "escudo:": "",
                        "imagen_recortada": "",
                    }];             
                }
            }

            function errorCallback(data){
                console.log(data);
            }  


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
                $scope.avatarInfo[0].UserId = $scope.user.UserId;
                $scope.avatarInfo[0].Alias = $scope.user.username;
                $scope.avatarInfo[0].Estrellas = $scope.user.stars;
                localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));

                $scope.scrollToTop();         
                $location.path('/Juegos/Avatar');

                //the next lines are related to the actual java integatration
//                $location.path('/ProgramaDashboard');
//                var avatarInfoForGameIntegration = {
//                        "UserId": $scope.avatarInfo[0].userid,
//                        "Alias": $scope.avatarInfo[0].alias,
//                        "Aplicacion": "Mi Avatar",
//                        "Estrellas": $scope.avatarInfo[0].estrellas,
//                        "PathImagen": "Android/data/<app-id>/images",
//                        "Color Cabello": $scope.avatarInfo[0].color_cabello,
//                        "Estilo Cabello": $scope.avatarInfo[0].estilo_cabello,
//                        "Traje color principal": $scope.avatarInfo[0].traje_color_principal,
//                        "Traje color secundario": $scope.avatarInfo[0].traje_color_secundario,
//                        "Rostro": $scope.avatarInfo[0].rostro,
//                       "Color de piel": $scope.avatarInfo[0].color_de_piel,
//                       "Escudo:": $scope.avatarInfo[0].escudo,
//                        "Imagen Recortada": $scope.avatarInfo[0].imagen_recortada,
//                    };    
//                cordova.exec(SuccessAvatar, FailureAvatar, "CallToAndroid", "openApp",[JSON.stringify(avatarInfoForGameIntegration)]);


            };
            
              function Success() {
                
            }
            
            function Failure() {
                
            }
            
            function SuccessAvatar(data) {
                    $scope.avatarInfo = [{
                        "userid": data["UserId"],
                        "alias": data["Alias"],
                        "aplicacion": data["Aplicacion"],
                        "estrellas": data["Estrellas"],
                        "PathImagen": "Android/data/<app-id>/images",
                        "color_cabello": data["Color Cabello"],
                        "estilo_cabello": data["Estilo Cabello"],
                        "traje_color_principal": data["Traje color principal"],
                        "traje_color_secundario": data["Traje color secundario"],
                        "rostro": data["Rostro"],
                        "color_de_piel": data["Color de piel"],
                        "escudo:": data["Escudo"],
                        "imagen_recortada": data["Imagen Recortada"],
                    }]; 

                  localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));
            }
            
            function FailureAvatar() {
            }

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
                $scope.scrollToTop();
            };
        }]);
