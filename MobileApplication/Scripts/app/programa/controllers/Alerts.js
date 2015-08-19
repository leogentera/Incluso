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
                {
                    notificationId: 1, notificationTitle:'Has ganado una estrella',
                    read:'false',
                    notificationDate:'01 de Enero de 1999',
                    notificationDetail : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras venenatis velit magna, vitae commodo metus volutpat a. Morbi euismod mauris lectus. Praesent suscipit consequat felis, a aliquet nibh porttitor vitae. Praesent volutpat tortor ipsum, tempor lacinia justo aliquet ut. Maecenas dolor mauris, vestibulum non varius a, feugiat in elit. Nulla libero elit, gravida quis orci pulvinar, consequat rhoncus ex. Praesent a ultrices nisi. Ut vel volutpat tortor, a ultrices nisl. Nullam at faucibus velit. Nunc pharetra lacus nisi.'
                    },
                {
                    notificationId: 2,
                    notificationTitle:'Has completado una act',
                    read: 'false',
                    notificationDate:'01 de Marzo de 1999',
                    notificationDetail : 'nteger ut convallis felis. Fusce in tincidunt nisl. Pellentesque quis neque leo. Aliquam eget lectus at sem gravida eleifend bibendum vel nulla. Curabitur quis augue non urna vestibulum sodales. Maecenas nec sem bibendum, pharetra turpis efficitur, hendrerit mi. Donec sit amet est ac dui ultricies dignissim. Fusce ac sodales tortor, in gravida leo. Nulla sem purus, varius sit amet nisi nec, interdum accumsan nisi. Fusce dolor urna, hendrerit quis interdum at, fringilla a orci. Sed dignissim ornare lectus, et tristique erat hendrerit eu. Vivamus ut consectetur ex, eu suscipit libero.'
                    },
                {
                    notificationId: 3,
                    notificationTitle:'Has completado una etapa',
                    read:'true',
                    notificationDate:'01 de Febrero de 1999',
                    notificationDetail : 'Ut consequat tristique est ac ultrices. Donec et euismod nisi. Proin sapien est, imperdiet quis ligula ac, efficitur sollicitudin metus. Sed nec risus arcu. Nam id blandit orci. Nam ornare fermentum lorem vel fermentum. Vivamus ex diam, eleifend et bibendum ac, varius a mi. Mauris commodo dolor neque, sed pretium lacus ultrices vitae'
                    }
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
            
            $scope.showAlertDetail = function (alertId) {                
                $location.path('/AlertsDetail/'+ alertId );                
            }
        }
]);
