// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.programa.tutorial', [])
    .controller('programaTutorialController', [
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

            $scope.scrollToTop();

            /* Models */
            $scope.hasSeenTutorial = moodleFactory.Services.GetCacheObject("HasSeenTutorial");

            /* Helpers */
            $scope.currentPage = 1;
            $scope.loading = false;

            //if ($scope.hasSeenTutorial && $scope.hasSeenTutorial == "true")
            //{
                //$location.path('/ProgramaDashboard');
            //}

            $scope.continue = function() {
                

				$scope.hasSeenTutorial = true;
                localStorage.setItem("HasSeenTutorial", "true");
                $scope.scrollToTop();
                $location.path('/Perfil');

            }

            $scope.navigateToPage = function(pageNumber){
                $scope.currentPage = pageNumber;
                $scope.scrollToTop();
            };
        }]);
