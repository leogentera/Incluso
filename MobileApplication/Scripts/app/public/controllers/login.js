// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.public.login', ['ui.select2', 'ngTagsInput'])
    .controller('publicLoginController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http) {

            $scope.userCredentialsModel = {
                username: "",
                password: "",
                rememberCredentials: false
            };

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

                $http(
                    {
                        method: 'POST',
                        url: API_RESOURCE.format("authentication"), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param({username: $scope.userCredentialsModel.username, password: $scope.userCredentialsModel.password})
                    }
                    ).success(function(data, status, headers, config) {

                        console.log('successfully logged in');

                        _setToken(data)


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
                        console.log('data' + data);
                        alert('error');
                    });
            }

            $scope.loginWithFacebook = function () {
                $location.path('/ProgramaDashboard');
            }

            $scope.loadCredentials();

        }]);