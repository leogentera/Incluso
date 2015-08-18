// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.public.recoverPassword', [])
    .controller('publicRecoverPasswordController', [
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

            $anchorScroll();
            $rootScope.showToolbar = false;
            $rootScope.showFooter = false;
            /* ViewModel */
            $scope.recoverPasswordModel = {
                email: "",
                secretQuestion: "",
                secretAnswer: "",
                password: "",
                confirmPassword: "",
                code: "",
                modelState: {
                    isValid: null,
                    errorMessages: []
                }
            };

            /* Helpers */
            var isConfirmedPasswordValid = false;
            $scope.currentPage = 1;
            $scope.successMessage = "";
            $scope.recoveredPassword = false;
            $scope.readOnly = false;
            $scope.PreloaderModalInstance = null;
            $rootScope.showToolbar = false;
            $rootScope.showFooter = false;

            /* Watchers */
            $scope.$watch("recoverPasswordModel.confirmPassword", function(newValue, oldValue){
                isConfirmedPasswordValid = (newValue === $scope.recoverPasswordModel.password);
            });
            $scope.$watch("recoverPasswordModel.password", function(newValue, oldValue){
                isConfirmedPasswordValid = (newValue === $scope.recoverPasswordModel.confirmPassword);
            });
            $scope.$watch("recoverPasswordModel.modelState.errorMessages", function(newValue, oldValue){
                $scope.recoverPasswordModel.modelState.isValid = (newValue.length === 0);
            });


            $scope.login = function() {
                $location.path('/');
            };

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
                $anchorScroll();
            };

            $scope.getPasswordRecoveryCode = function() {
                console.log('Start Code Recovery'); //- debug
                console.log('fetching errors list'); //- debug
                var errors = [];
                if(!$scope.recoverPasswordForm.email.$valid){ errors.push("Formato de correo incorrecto."); }
                if($scope.recoverPasswordModel.secretQuestion.length === 0){ errors.push("Pregunta secreta inválida."); }
                if(!$scope.recoverPasswordForm.secretAnswer.$valid){ errors.push("Respuesta secreta inválida."); }
                $scope.recoverPasswordModel.modelState.errorMessages = errors;

                console.log('validating'); //- debug
                if(errors.length === 0){
                    console.log('errors: ' + errors.length); //- debug
                    $scope.openProcessingActionModal();

                    $http({
                        method: 'POST',
                        url: API_RESOURCE.format("authentication"), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param({
                            email: $scope.recoverPasswordModel.email,
                            secretanswer: $scope.recoverPasswordModel.secretAnswer.toString().toLowerCase(),
                            secretquestion: $scope.recoverPasswordModel.secretQuestion,
                            action: "forgot"
                        })
                    }).success(function(data, status, headers, config) {

                        console.log('SUCCESS. code recovered'); //- debug
                        $scope.PreloaderModalInstance.dismiss();
                        $scope.currentPage = 2;
                        $scope.scrollToTop();
                        $scope.successMessage = "Te hemos enviado un correo con un código para recuperar tu contraseña.";
                        $scope.scrollToTop();

                    }).error(function(data, status, headers, config) {                                            
                        console.log('ERROR. code not recovered'); //- debug
                        $scope.PreloaderModalInstance.dismiss();
                        var errorMessage;
                        if((data != null && data.messageerror != null)){
                            errorMessage = window.atob(data.messageerror);
                        }else{
                            errorMessage = "Se ha producido un error, contactate al administrador."
                        }

                        $scope.recoverPasswordModel.modelState.errorMessages = [errorMessage];
                        console.log('message: ' + errorMessage); //- debug
                        $scope.scrollToTop();
                    });
                }else{
                    console.log('errors: ' + errors.length); //- debug
                    console.log('End'); //- debug
                    $scope.scrollToTop();
                }
            }

            $scope.recover = function() {
                console.log('Start Password Reset'); //- debug
                console.log('fetching errors list'); //- debug
                var errors = [];
                var passwordPolicy = "debe ser almenos de 8 caracterres, incluir un caracter especial, una letra mayúscula, una minúscula y un número.";
                if(!isConfirmedPasswordValid) { errors.push("la confirmación de contraseña no coincide con la contraseña."); }
                if(!$scope.recoverPasswordForm.password.$valid){ errors.push("formato de contraseña incorrecto.  La contraseña " + passwordPolicy); }
                if(!$scope.recoverPasswordForm.confirmPassword.$valid){ errors.push("formato de confirmación de contraseña incorrecto. La confirmación de contraseña " + passwordPolicy); }
                if(!$scope.recoverPasswordForm.code.$valid){ errors.push("código requerido."); }
                $scope.recoverPasswordModel.modelState.errorMessages = errors;

                console.log('validating'); //- debug
                if(errors.length === 0){
                    console.log('errors: ' + errors.length); //- debug
                    $scope.openProcessingActionModal();

                    $http({
                        method: 'PUT',
                        url: API_RESOURCE.format("authentication"), 
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param({
                            email: $scope.recoverPasswordModel.email,
                            password: $scope.recoverPasswordModel.password,
                            recoverycode: $scope.recoverPasswordModel.code
                        })
                    }).success(function(data, status, headers, config) {

                        console.log('SUCCESS. password reset'); //- debug
                        $scope.PreloaderModalInstance.dismiss();
                        $scope.recoveredPassword = true;
                        $scope.successMessage = "Se ha restablecido su contraseña, ahora puedes iniciar sesión.";
                        $anchorScroll();

                        //$scope.recoverPasswordModel.password = "";
                        //$scope.recoverPasswordModel.confirmPassword = "";
                        //$scope.recoverPasswordModel.code = "";
                        $scope.readOnly = true;

                    }).error(function(data, status, headers, config) {
                        
                        console.log('ERROR. password not reset'); //- debug
                        $scope.PreloaderModalInstance.dismiss();
                        var errorMessage;
                        if((data != null && data.messageerror != null)){
                            errorMessage = window.atob(data.messageerror);
                        }else{
                            errorMessage = "Se ha producido un error, contactate al administrador."
                        }

                        $scope.recoverPasswordModel.modelState.errorMessages = [errorMessage];
                        console.log('message: ' + errorMessage); //- debug
                        $scope.scrollToTop();
                    });
                }else{
                    console.log('errors: ' + errors.length); //- debug
                    console.log('End'); //- debug
                    $scope.scrollToTop();
                }
            };

            /* open processing action modal */
            $scope.openProcessingActionModal = function (size) {
                $scope.PreloaderModalInstance = $modal.open({
                    templateUrl: 'processingActionModal.html',
                    controller: 'processingActionModalController',
                    windowClass: 'modal-theme-default modal-preloader',
                    size: size,
                    backdrop: 'static',
                    keyboard: true,
                    animation: true,
                });
                //$scope.PreloaderModalInstance = modalInstance;
            };            
        }])
        .controller('processingActionModalController', function ($scope, $modalInstance) {
            
        });


