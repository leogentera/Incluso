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
                                
            
            $scope.register = function() {
                
                console.log('register');
                localStorage.removeItem("Credentials");                            
                
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
                                    //console.log('redirecting..');
                                    //$location.path('/ProgramaDashboard');
                                    
                                    console.log('redirecting to tutorial..');
                                    $location.path('/Tutorial');
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
                            //birthday: $scope.registerModel.birthday,
                            birthday: dpValue,
                            gender: $scope.registerModel.gender
                        })
                    }).success(function(data, status, headers, config) {

                        $scope.isRegistered = true;
                        //initModel();

                        console.log('successfully register');
                        $scope.scrollToTop();
                         $scope.autologin();

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

            function calculate_age()
            {                
                var birth_month = dpValue.substring(0,2);
                var birth_day = dpValue.substring(3,5);
                var birth_year = dpValue.substring(6,10);
                today_date = new Date();
                today_year = today_date.getFullYear();
                today_month = today_date.getMonth();
                today_day = today_date.getDate();
                age = today_year - birth_year;
            
                if ( today_month < (birth_month - 1))
                {
                    age--;
                }
                if (((birth_month - 1) == today_month) && (today_day < birth_day))
                {
                    age--;
                }
                return age;
            }
            

            function validateModel(){
                var errors = [];

                var datePickerValue =  $("input[name=birthday]").value();
                dpValue = moment(datePickerValue).format("DD/MM/YYYY");
                
                var passwordPolicy = "debe ser almenos de 8 caracterres, incluir un caracter especial, una letra mayúscula, una minúscula y un número";       
                
                if(!$scope.registerForm.password.$valid)
                { 
                    errors.push("formato de contraseña incorrecto. La contraseña " + passwordPolicy); 
                }
                else
                {
                    if(!$scope.registerForm.confirmPassword.$valid)
                    { 
                        errors.push("formato de confirmación de contraseña incorrecto. La confirmacion de contraseña " + passwordPolicy); 
                    }
                    else
                    {
                        if(!isConfirmedPasswordValid) { errors.push("la confirmación de contraseña no coincide con la contraseña."); }
                    }     
                }

                if(!$scope.registerForm.username.$valid){ errors.push("formato de usuario incorrecto."); }                
                if($scope.registerModel.gender.length === 0){ errors.push("Género inválido."); }
                if($scope.registerModel.country.length === 0){ errors.push("País inválido."); }
                if($scope.registerModel.city.length === 0){ errors.push("Ciudad inválida."); }
                if(!$scope.registerForm.email.$valid){ errors.push("formato de correo incorrecto."); }                
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
                    size: size,
                    windowClass: 'modal-theme-default',
                    backdrop: 'static'
                });
            };
        }])
        .controller('termsAndConditionsController', function ($scope, $modalInstance) {
            $scope.cancel = function () {
                $modalInstance.dismiss('cancel');
            };
        });
