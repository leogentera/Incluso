// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.public.register', ['ui.select2', 'ngTagsInput'])
    .controller('publicRegisterController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http) {
            
            var isConfirmedPasswordValid = false;

            $scope.registerModel = {
                username: "",
                birthday: "",
                gender: "",
                country: "",
                city: "",
                email: "",
                password: "",
                confirmPassword: "",
                secretQuestion: "",
                secretAnswer: ""
            };

            $scope.$watch("registerModel.confirmPassword", function(newValue, oldValue){
                isConfirmedPasswordValid = (newValue === $scope.registerModel.password);
            });

            $scope.register = function() {
                
                console.log('register');

                if(validateForm()){
                    registerUser();
                }
            }


            $scope.cancel = function() {
                $location.path('/');
            }

            var registerUser = function(){

                $http({
                        method: 'POST',
                        url: API_RESOURCE.format("register"), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param({
                            username: $scope.registerModel.username,
                            password: $scope.registerModel.password,
                            email: $scope.registerModel.email,
                            city: $scope.registerModel.city,
                            country: $scope.registerModel.country,
                            secretanswer: $scope.registerModel.secretAnswer,
                            secretquestion: $scope.registerModel.secretQuestion,
                            birthday: $scope.registerModel.birthday,
                            gender: $scope.registerModel.gender
                        })
                    }).success(function(data, status, headers, config) {

                        console.log('successfully register');
                        console.log('preparing for syncAll');
                        console.log('redirecting..');
                        
                        $timeout(
                                function() {
                                    console.log('redirecting..');
                                    $location.path('/');
                                },1000);


                    }).error(function(data, status, headers, config) {
                        console.log('data' + data);
                        alert('error');
                    });
            };

            var validateForm = function(){

                var isValid = true;

                /* validate password */
                if(!isConfirmedPasswordValid){

                    isValid = isValid && isConfirmedPasswordValid;
                    alert("la confirmación de contraseña no coincide con la contraseña");
                }

                return isValid;
            };


        }]);


