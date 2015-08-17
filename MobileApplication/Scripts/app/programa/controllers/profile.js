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
            
            _httpFactory = $http;
            
            $scope.currentPage = 1;
            $scope.model = getDataAsync(); 
            $rootScope.pageName = "Mi perfil"
            $rootScope.navbarBlue = false;
            $rootScope.showToolbar = true;
            $rootScope.showFooter = true;

            function getDataAsync(){

                moodleFactory.Services.GetAsyncAvatar(_getItem("userId"), getAvatarInfoCallback);
                var m = JSON.parse(moodleFactory.Services.GetCacheObject("profile"));

              
                if (!m) {
                    $location.path('/');
                    return "";
                }
                initFields(m);

                return m;
            }

            function initFields(m){
                if(m.address.street == null){ m.address.street = ""; }
                if(m.address.num_ext == null){ m.address.num_ext = ""; }
                if(m.address.num_int == null){ m.address.num_int = ""; }
                if(m.address.colony == null){ m.address.colony = ""; }
                if(m.address.city == null){ m.address.city = ""; }
                if(m.address.town == null){ m.address.town = ""; }
                if(m.address.state == null){ m.address.state = ""; }
                if(m.address.postalCode == null){ m.address.postalCode = ""; }                
            }

            function getAvatarInfoCallback(){

                $scope.avatarInfo = moodleFactory.Services.GetCacheJson("avatarInfo");

                if ($scope.avatarInfo == null || $scope.avatarInfo.length == 0) {
                    $scope.avatarInfo = [{
                        "userid": $scope.model.UserId,
                        "alias": $scope.model.username,
                        "aplicacion": "Mi Avatar",
                        "estrellas": $scope.model.stars,
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
                moodleFactory.Services.PutAsyncProfile(_getItem("userId"), $scope.model,
                    function(){
                        $scope.index();
                    },
                    function(){
                        $scope.index();
                    });
                $scope.index();
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
                var existingEmail = $scope.model.email;
                if (existingEmail) {
                    $scope.model.additionalEmails.push(new String());
                }
            }
            
            $scope.logout = function(){
                logout($http, $scope, $location);
            };
            
            $scope.deleteAdditionalEmails = function(index){                
                $scope.model.additionalEmails.splice(index,1);
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

            $scope.avatar = function(){
                $scope.avatarInfo[0].UserId = $scope.model.UserId;
                $scope.avatarInfo[0].Alias = $scope.model.username;
                $scope.avatarInfo[0].Estrellas = $scope.model.stars;
                localStorage.setItem("avatarInfo", JSON.stringify($scope.avatarInfo));

                $scope.scrollToTop();         
                $location.path('/Juegos/Avatar');
            }

            var $selects = $('select.form-control');
            $selects.change(function(){
                $elem = $(this);
                $elem.addClass('changed');
            });
        }]);
