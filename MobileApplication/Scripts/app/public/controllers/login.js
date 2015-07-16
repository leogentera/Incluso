// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.public.login', [])
    .controller('publicLoginController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        '$anchorScroll',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http, $anchorScroll) {

            $scope.scrollToTop();

            /* ViewModel */
            $scope.userCredentialsModel = {
                username: "",
                password: "",
                rememberCredentials: false,
                modelState: {
                    isValid: null,
                    errorMessages: []
                }
            };

            /* Watchers */
            $scope.$watch("userCredentialsModel.modelState.errorMessages", function(newValue, oldValue){
                $scope.userCredentialsModel.modelState.isValid = (newValue.length === 0);
            });

            $scope.loadCredentials = function() {
                var txtCredentials = localStorage.getItem("Credentials");
                var userCredentials = null;

                if (txtCredentials){
                    userCredentials = JSON.parse(txtCredentials);

                    $scope.userCredentialsModel.username = userCredentials.username;
                    $scope.userCredentialsModel.password = userCredentials.password;
                    $scope.userCredentialsModel.rememberCredentials = userCredentials.rememberCredentials;
                }

                //autologin
                if (_IsOffline() && userCredentials) {
                    $location.path('/ProgramaDashboard');
                } 
            }

            $scope.login = function (username, password) {
                
                console.log('login in');

                if(validateModel()){

                    $http(
                    {
                        method: 'POST',
                        url: API_RESOURCE.format("authentication"), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param({username: $scope.userCredentialsModel.username, password: $scope.userCredentialsModel.password})
                    }
                    ).success(function(data, status, headers, config) {

                        console.log('successfully logged in');

                        _setToken(data.token);

                        //keepUserInformation(data.id);


                        console.log('preparing for syncAll');

                        //succesful credentials
                        _syncAll(function() {
                            console.log('came back from redirecting...');
                            $timeout(
                                function() {
                                    console.log('redirecting..');
                                    $location.path('/ProgramaDashboard');
                                },1000);
                        });

                        if ($scope.userCredentialsModel.rememberCredentials) {
                            localStorage.setItem("Credentials", JSON.stringify($scope.userCredentialsModel));
                        } else {
                            localStorage.removeItem("Credentials");
                        }

                    }).error(function(data, status, headers, config) {
                        var errorMessage = window.atob(data.messageerror);

                        $scope.userCredentialsModel.modelState.errorMessages = [errorMessage];
                        console.log(status + ": " + errorMessage);
                        $scope.scrollToTop();
                    });
                }else{
                    $scope.scrollToTop();
                }
            }

            $scope.loginWithFacebook = function () {
                $location.path('/ProgramaDashboard');
            }

            function validateModel(){
                var errors = [];

                if(!$scope.loginForm.username.$valid){ errors.push("formato de usuario incorrecto."); }
                if(!$scope.loginForm.password.$valid){ errors.push("formato de contraseña incorrecto."); }

                $scope.userCredentialsModel.modelState.errorMessages = errors;

                return (errors.length === 0);
            }

            function keepUserInformation(userId){

                $http(
                    {
                        method: 'GET',
                        url: API_RESOURCE.format("userprofile/" + userId), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    }
                    ).success(function(data, status, headers, config) {

                        localStorage.setItem("profile", JSON.stringify(data));
                    }).error(function(data, status, headers, config) {
                    });

            }

            $scope.loadCredentials();

        }]);