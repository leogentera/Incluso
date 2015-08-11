// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.public.register', [])
    .controller('publicRegisterController', [
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
            
            
            var dpValue;    
            $("input[name=birthday]").datepicker({
                dateFormat: "d M, y"
            });
                                
            $scope.scrollToTop();

            /* ViewModel */
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
                termsAndConditions: false,
                modelState: {
                    isValid: null,
                    errorMessages: []
                }
            };

            $scope.currentUserModel = {
                token: "",
                userId: ""
            };

            
            /* Helpers */
            var isConfirmedPasswordValid = false;
            $scope.currentPage = 1;
            $scope.isRegistered = false;

            /* Watchers */
            $scope.$watch("registerModel.confirmPassword", function(newValue, oldValue){
                isConfirmedPasswordValid = (newValue === $scope.registerModel.password);
            });
            $scope.$watch("registerModel.password", function(newValue, oldValue){
                isConfirmedPasswordValid = (newValue === $scope.registerModel.confirmPassword);
            });
            $scope.$watch("registerModel.modelState.errorMessages", function(newValue, oldValue){
                $scope.registerModel.modelState.isValid = (newValue.length === 0);
            });
             
            $scope.$watch("registerModel.birthday", function(newValue, oldValue){                
                if (newValue) {
                    var datePickerValue =  $("input[name=birthday]").datepicker("getDate");
                    dpValue = moment(datePickerValue).format("L");                    
                }                            
            });

            $scope.showCalendar = function(){                
              $("#datePicker").toggle();              
            }                        
            
            $scope.register = function() {
                
                console.log('register');

                if(validateModel()){
                    registerUser();
                }else{
                    $scope.scrollToTop();
                }
            }

            $scope.autologin = function(){
                console.log('login in');

                    $http(
                    {
                        method: 'POST',
                        url: API_RESOURCE.format("authentication"), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param({username: $scope.registerModel.username, password: $scope.registerModel.password})
                    }
                    ).success(function(data, status, headers, config) {

                        console.log('successfully logged in');

                        //save token for further requests and autologin
                        $scope.currentUserModel.token = data.token;
                        $scope.currentUserModel.userId = data.id;

                        localStorage.setItem("CurrentUser", JSON.stringify($scope.currentUserModel));

                        _setToken(data.token);
                        _setId(data.id);

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

                    }).error(function(data, status, headers, config) {
                        var errorMessage = window.atob(data.messageerror);

                        $scope.registerModel.modelState.errorMessages = [errorMessage];
                        console.log(status + ": " + errorMessage);
                        $scope.scrollToTop();
                    });

            }

            $scope.login = function() {
                $location.path('/');
            }

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
                $scope.scrollToTop();
            };

            var registerUser = function(){
                
                $http({
                        method: 'POST',
                        url: API_RESOURCE.format("user"), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param({
                            username: $scope.registerModel.username,
                            password: $scope.registerModel.password,
                            email: $scope.registerModel.email,
                            city: $scope.registerModel.city,
                            country: $scope.registerModel.country,
                            secretanswer: $scope.registerModel.secretAnswer,
                            secretquestion: $scope.registerModel.secretQuestion,
                            birthday: dpValue,
                            gender: $scope.registerModel.gender
                        })
                    }).success(function(data, status, headers, config) {

                        $scope.isRegistered = true;
                        //initModel();

                        console.log('successfully register');
                        $scope.scrollToTop();

                    }).error(function(data, status, headers, config) {
                        var errorMessage;

                        if((data != null && data.messageerror != null)){
                            errorMessage = window.atob(data.messageerror);
                        }else{
                            errorMessage = "Se ha producido un error, contactate al administrador."
                        }

                        $scope.registerModel.modelState.errorMessages = [errorMessage];
                        console.log('data' + errorMessage);
                        $scope.scrollToTop();
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
                if(!$scope.registerModel.termsAndConditions){ errors.push("Debe aceptar los términos y condiciones."); }

                $scope.registerModel.modelState.errorMessages = errors;

                return (errors.length === 0);
            }

            function initModel(){

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
                    termsAndConditions: false,
                    modelState: {
                        isValid: null,
                        errorMessages: []
                    }
                };

            }

            /* open terms and conditions modal */
            $scope.openModal = function (size) {
                var modalInstance = $modal.open({
                    animation: $scope.animationsEnabled,
                    templateUrl: 'termsAndConditionsModal.html',
                    controller: 'termsAndConditionsController',
                    size: size
                });
            };
        }])
        .controller('termsAndConditionsController', function ($scope, $modalInstance) {
            $scope.cancel = function () {
                $modalInstance.dismiss('cancel');
            };
        });
