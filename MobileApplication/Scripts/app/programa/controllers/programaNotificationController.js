angular
    .module('incluso.programa.notificationcontroller', [])
    .controller('programaNotificationController', [
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

            $rootScope.pageName = "Notificaciones"
            $rootScope.navbarBlue = false;
            $rootScope.showToolbar = true;
            $rootScope.showFooter = true; 
            $rootScope.showFooterRocks = false; 

            $scope.notifications = [{
                    notificationId: 1,
                    notificationTitle:'Has ganado una estrella',
                    read:'false',
                    notificationDate:'01 de Enero de 1999',
                    notificationDetail : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras venenatis velit magna, vitae commodo metus volutpat a. Morbi euismod mauris lectus. Praesent suscipit consequat felis, a aliquet nibh porttitor vitae. Praesent volutpat tortor ipsum, tempor lacinia justo aliquet ut. Maecenas dolor mauris, vestibulum non varius a, feugiat in elit. Nulla libero elit, gravida quis orci pulvinar, consequat rhoncus ex. Praesent a ultrices nisi. Ut vel volutpat tortor, a ultrices nisl. Nullam at faucibus velit. Nunc pharetra lacus nisi.'
                },
                {
                    notificationId: 2,
                    notificationTitle:'Has ganado dos estrellas',
                    read:'false',
                    notificationDate:'01 de Febrero de 1999',
                    notificationDetail : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras venenatis velit magna, vitae commodo metus volutpat a. Morbi euismod mauris lectus. Praesent suscipit consequat felis, a aliquet nibh porttitor vitae. .'
                },
                {
                    notificationId: 3,
                    notificationTitle:'Has ganado tres estrellas',
                    read:'false',
                    notificationDate:'01 de MArzo de 1999',
                    notificationDetail : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras venenatis velit magna, vitae commodo metus volutpat a..'
                }
            ];
                                
            $scope.notification = _.find($scope.notifications, function(d){return d.notificationId == $routeParams.notificationId; })
            
            $scope.scrollToTop();
            $scope.$emit('HidePreloader'); //hide preloader

            $scope.back = function () {
                $location.path('/ProgramaDashboard');
            }
                                
        }]);