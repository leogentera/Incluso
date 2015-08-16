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
        '$modal',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http, $anchorScroll, $modal) {

            _httpFactory = $http;
            $scope.PreloaderModalInstance = null;

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

            $scope.currentUserModel = {
                token: "",
                userId: ""
            };

            /* Watchers */
            $scope.$watch("userCredentialsModel.modelState.errorMessages", function (newValue, oldValue) {
                $scope.userCredentialsModel.modelState.isValid = (newValue.length === 0);
            });

            $(".navbar-absolute-top").hide();

            $scope.loadCredentials = function () {

                var txtCredentials = localStorage.getItem("Credentials");
                var txtCurrentUser = localStorage.getItem("CurrentUser");
                var userCredentials = null;
                var currentUser = null;

                console.log('loading..');


                if (txtCredentials) {
                    userCredentials = JSON.parse(txtCredentials);

                    $scope.userCredentialsModel.username = userCredentials.username;
                    $scope.userCredentialsModel.password = userCredentials.password;
                    $scope.userCredentialsModel.rememberCredentials = userCredentials.rememberCredentials;
                }

                if (txtCurrentUser) {
                    currentUser = JSON.parse(txtCurrentUser);

                    $scope.currentUserModel.token = currentUser.token;
                    $scope.currentUserModel.userId = currentUser.userId;
                }

                //autologin
                if (currentUser && currentUser.token && currentUser.token != "") {
                    $location.path('/ProgramaDashboard');
                }
            }

            $scope.login = function (username, password) {

                console.log('login in');

                if (validateModel()) {

                    // reflect loading state at UI
                    $scope.openProcessingActionModal();
                    $scope.isLogginIn = true;  

                    $http(
                        {
                            method: 'POST',
                            url: API_RESOURCE.format("authentication"),
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            data: $.param({ username: $scope.userCredentialsModel.username.toString().toLowerCase(), password: $scope.userCredentialsModel.password })
                        }
                        ).success(function (data, status, headers, config) {

                            console.log('successfully logged in');
                            //$scope.PreloaderModalInstance.dismiss();

                            //save token for further requests and autologin
                            $scope.currentUserModel.token = data.token;
                            $scope.currentUserModel.userId = data.id;

                            localStorage.setItem("CurrentUser", JSON.stringify($scope.currentUserModel));

                            _setToken(data.token);
                            _setId(data.id);

                            console.log('preparing for syncAll');                            
                            
                            //succesful credentials
                            _syncAll(function () {
                                console.log('came back from redirecting...');
                                $timeout(
                                    function () {
                                        //possible line for modal dismiss
                                        console.log('redirecting..');
                                        $scope.PreloaderModalInstance.dismiss();

                                        $location.path('/ProgramaDashboard');
                                    }, 1000);
                            });

                            if ($scope.userCredentialsModel.rememberCredentials) {
                                localStorage.setItem("Credentials", JSON.stringify($scope.userCredentialsModel));
                            } else {
                                localStorage.removeItem("Credentials");
                            }

                        }).error(function (data, status, headers, config) {
                            
                            $scope.PreloaderModalInstance.dismiss();
                            var errorMessage = window.atob(data.messageerror);                            
                            $scope.userCredentialsModel.modelState.errorMessages = [errorMessage];
                            console.log(status + ": " + errorMessage);
                            $scope.scrollToTop();
                            $scope.isLogginIn = false;
                        });
                } else {
                    $scope.scrollToTop();
                }
            }

            $scope.loginWithFacebook = function () {
                
                //$location.path('/ProgramaDashboard');
                debugger
                var name = API_RESOURCE.format("")
                name = name.substring(0, name.length - 1);                                
                
                cordova.exec(FacebookLoginSuccess, FacebookLoginFailure, "SayHelloPlugin", "connectWithFacebook", [name]);
            }
            
            $scope.scrollToTop = function(){
                $anchorScroll();
            }

            function FacebookLoginSuccess(data) {
                console.log('successfully logged in ' + data);
                
                var userFacebook = JSON.parse(data);

                //save token for further requests and autologin
                $scope.currentUserModel.token = userFacebook.token;
                $scope.currentUserModel.userId = userFacebook.id;

                localStorage.setItem("CurrentUser", JSON.stringify($scope.currentUserModel));

                _setToken(userFacebook.token);
                _setId(userFacebook.id);

                console.log('preparing for syncAll');

                //succesful credentials
                _syncAll(function () {
                    console.log('came back from redirecting...');
                    $timeout(
                        function () {
                            console.log('redirecting..');
                            $location.path('/ProgramaDashboard');
                        }, 1000);
                });

                if ($scope.userCredentialsModel.rememberCredentials) {
                    localStorage.setItem("Credentials", JSON.stringify($scope.userCredentialsModel));
                } else {
                    localStorage.removeItem("Credentials");
                }
            }

            function FacebookLoginFailure(data) {
                var errorMessage = window.atob(data.messageerror);//window.atob("Could not authenticate with facebook");
                console.log('Could not authenticate with facebook ' + data);
                
                $scope.userCredentialsModel.modelState.errorMessages = [errorMessage];
                console.log(status + ": " + errorMessage);
                $scope.scrollToTop();
            }

            function validateModel() {
                var errors = [];

                if (!$scope.loginForm.username.$valid) { errors.push("formato de usuario incorrecto."); }
                if (!$scope.loginForm.password.$valid) { errors.push("formato de contraseña incorrecto."); }

                $scope.userCredentialsModel.modelState.errorMessages = errors;

                return (errors.length === 0);
            }

            function keepUserInformation(userId) {

                $http(
                    {
                        method: 'GET',
                        url: API_RESOURCE.format("userprofile/" + userId),
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    }
                    ).success(function (data, status, headers, config) {

                        localStorage.setItem("profile", JSON.stringify(data));
                    }).error(function (data, status, headers, config) {
                    });

            }

            $scope.loadCredentials();

            // $location.path('/ProgramaDashboardEtapa/' + 1);

            /* open processing action modal */
            $scope.openProcessingActionModal = function (size) {
                var modalInstance = $modal.open({
                    animation: true,
                    templateUrl: 'processingActionModal.html',
                    controller: 'processingActionModalController',
                    size: size,
                    windowClass: 'modal-theme-default modal-preloader',
                    backdrop: 'static'
                });
                $scope.PreloaderModalInstance = modalInstance;
            };            

        }])
        .controller('processingActionModalController', function ($scope, $modalInstance) {
            
        });
