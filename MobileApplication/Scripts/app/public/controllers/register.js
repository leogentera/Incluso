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

            $scope.currentPage = 1;

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
                secretAnswer: "",
                modelState: {
                    isValid: null,
                    errorMessages: []
                }
            };

            $scope.$watch("registerModel.confirmPassword", function(newValue, oldValue){
                isConfirmedPasswordValid = (newValue === $scope.registerModel.password);
            });

            $scope.$watch("registerModel.password", function(newValue, oldValue){
                isConfirmedPasswordValid = (newValue === $scope.registerModel.confirmPassword);
            });

            $scope.$watch("registerModel.modelState.errorMessages", function(newValue, oldValue){
                $scope.registerModel.modelState.isValid = (newValue.length === 0);
            });

            $scope.register = function() {
                
                console.log('register');

                if(validateModel()){
                    registerUser();
                }
            }

            $scope.cancel = function() {
                $location.path('/');
            }

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
            };

            var registerUser = function(){

                $http({
                        method: 'POST',
                        url: API_RESOURCE.format("Register"), 
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
                        $scope.registerModel.modelState.errorMessages = [data.messageerror];
                        console.log('data' + data);
                    });
            };


            function validateModel(){
                var errors = [];

                if(!isConfirmedPasswordValid) { errors.push("la confirmación de contraseña no coincide con la contraseña."); }

                if(!$scope.registerForm.username.$valid){ errors.push("formato de usuario incorrecto."); }
                if(!$scope.registerForm.birthday.$valid){ errors.push("Fecha de nacimiento incorrecta."); }
                if($scope.registerModel.gender.length === 0){ errors.push("Género inválido."); }
                if($scope.registerModel.country.length === 0){ errors.push("País inválido."); }
                if($scope.registerModel.city.length === 0){ errors.push("Ciudad inválida."); }
                if(!$scope.registerForm.email.$valid){ errors.push("formato de correo incorrecto."); }
                if(!$scope.registerForm.password.$valid){ errors.push("formato de contraseña incorrecto."); }
                if(!$scope.registerForm.confirmPassword.$valid){ errors.push("formato de confirmación de contraseña incorrecto."); }
                if($scope.registerModel.secretQuestion.length === 0){ errors.push("Pregunta secreta inválida."); }
                if(!$scope.registerForm.secretAnswer.$valid){ errors.push("respuesta secreta inválida."); }

                $scope.registerModel.modelState.errorMessages = errors;

                return (errors.length === 0);
            }

        }]);


