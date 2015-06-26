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

            $scope.loadCredentials = function() {
                var txtCredentials = localStorage.getItem("Credentials");
                var userCredentials = null;

                if (txtCredentials){
                    userCredentials = JSON.parse(txtCredentials);
                }


                //autologin
                if (_IsOffline() && userCredentials) {
                    $location.path('/ProgramaDashboard');
                }

                //set credentials
                if (userCredentials) {
                    $timeout(
                        function() {
                            $("[name='username']").val(userCredentials.username);
                            $("[name='password']").val(userCredentials.password);
                        },1000);
               }
            }

            $scope.login = function (username, password) {
                var userCredentials = {
                    username: $("[name='username']").val(),
                    password: $("[name='password']").val(),
                    rememberCredentials: $("[name='rememberCredentials']")[0].checked,
                };

                $http(
                    {
                        method: 'POST',
                        url: API_RESOURCE.format("authentication"), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param({username:userCredentials.username, password:userCredentials.password})
                    }
                    ).success(function(data, status, headers, config) {
                        //succesful credentials
                        //_syncAll(function() {
                        //    $location.path('/ProgramaDashboard');
                        //});

                        if (userCredentials.rememberCredentials) {
                            localStorage.setItem("Credentials", JSON.stringify(userCredentials));
                        } else {
                            localStorage.removeItem("Credentials");
                        }

                        $location.path('/ProgramaDashboard');
                    }).error(function(data, status, headers, config) {
                        alert('error');
                    });
            }

            $scope.loginWithFacebook = function () {
                $location.path('/ProgramaDashboard');
            }

            $scope.loadCredentials();

        }]);