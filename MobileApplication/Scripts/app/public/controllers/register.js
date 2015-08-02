// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.public.register', [])
    .controller('publicRegisterController', [
        '$q',
        '$scope',
        '$modal',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        '$anchorScroll',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http, $anchorScroll, $modal) {

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

                if(validateModel()){
                    registerUser();
                }else{
                    $scope.scrollToTop();
                }
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
                            birthday: $scope.registerModel.birthday,
                            gender: $scope.registerModel.gender
                        })
                    }).success(function(data, status, headers, config) {

                        $scope.isRegistered = true;
                        initModel();

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

                $scope.items = ['item1', 'item2', 'item3'];

                var modalInstance = $modal.open({
                    animation: $scope.animationsEnabled,
                    templateUrl: 'termsAndConditionsModal.html',
                    controller: 'ModalInstanceCtrl',
                    size: size
                });

                /*modalInstance.result.then(
                    function (selectedItem) {
                        $scope.selected = selectedItem;
                    }, 
                    function () {
                        $log.info('Modal dismissed at: ' + new Date());
                    }
                );*/
            };

        }])


        .controller('modalServiceControllerA', function ($scope, $modal, $log) {

            $scope.items = ['item1', 'item2', 'item3'];

            $scope.animationsEnabled = true;

            $scope.open = function (size) {

                var modalInstance = $modal.open({
                    animation: $scope.animationsEnabled,
                    templateUrl: 'termsAndConditionsModal.html',
                    controller: 'ModalInstanceCtrl',
                    size: size,
                    resolve: {
                        items: function () {
                            return $scope.items;
                        }
                    }
                });

                modalInstance.result.then(
                    function (selectedItem) {
                        $scope.selected = selectedItem;
                    }, 
                    function () {
                        $log.info('Modal dismissed at: ' + new Date());
                    }
                );
            };

            $scope.toggleAnimation = function () {
                $scope.animationsEnabled = !$scope.animationsEnabled;
            };

        })

        // Please note that $modalInstance represents a modal window (instance) dependency.
        // It is not the same as the $modal service used above.

        .controller('ModalInstanceCtrl', function ($scope, $modalInstance, items) {

            $scope.items = items;
            $scope.selected = {
                item: $scope.items[0]
            };

            $scope.ok = function () {
                $modalInstance.close($scope.selected.item);
            };

            $scope.cancel = function () {
                $modalInstance.dismiss('cancel');
            };
        });
