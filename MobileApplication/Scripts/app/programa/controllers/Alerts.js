angular
    .module('incluso.program.alerts', [])
    .controller('AlertsController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
        '$timeout',
        '$rootScope',
        '$http',
        '$modal',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http, $modal) {
            
            $scope.notifications = [
                { notificationDetail:'Has ganado una estrella', read:'false', notificationDate:'01 de Enero de 1999'},
                { notificationDetail:'Has completado una act', read: 'false', notificationDate:'01 de Marzo de 1999'},
                { notificationDetail:'Has completado una etapa', read:'true', notificationDate:'01 de Febrero de 1999'}
            ];
            
            $rootScope.pageName = "Notificaciones";
            $rootScope.navbarBlue = false;
            $rootScope.showToolbar = true;
            $rootScope.showFooter = true;
            $rootScope.showFooterRocks = false;
                                            
            $scope.read = function(item){
                return item.read == read;                
            }
                    
            $scope.$emit('HidePreloader');
            
            $scope.back = function () {
                $location.path('/ProgramaDashboard');
            }
            
        }
]);
