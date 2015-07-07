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

            var validateModel = function(){

                var errors = [];

                if(!isConfirmedPasswordValid) { errors.push("la confirmación de contraseña no coincide con la contraseña."); }
                if($scope.registerModel.username.length === 0){ errors.push("Usuario inválido."); }
                if($scope.registerModel.birthday.length === 0){ errors.push("Fecha de Nacimiento inválida."); }
                if($scope.registerModel.gender.length === 0){ errors.push("Género inválido."); }
                if($scope.registerModel.country.length === 0){ errors.push("País inválido."); }
                if($scope.registerModel.city.length === 0){ errors.push("Ciudad inválido."); }
                if(!validateEmail($scope.registerModel.email)){ errors.push("Email inválido."); }
                if($scope.registerModel.password.length === 0){ errors.push("Contraseña inválida."); }
                if($scope.registerModel.confirmPassword.length === 0){ errors.push("Confirmación de contraseña inválida."); }
                if($scope.registerModel.secretQuestion.length === 0){ errors.push("Pregunta secreta inválida."); }
                if($scope.registerModel.secretAnswer.length === 0){ errors.push("Respuesta secreta inválida."); }

                $scope.registerModel.modelState.errorMessages = errors;

                return (errors.length === 0);
            };

            var validateEmail = function (email) {
                var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                return re.test(email);
            }

        }]);


