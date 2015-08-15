angular
    .module('incluso.home', [])
    .controller('homeCtrl', [
        '$rootScope',
        '$scope',
        '$location',
        '$anchorScroll',
        function ($rootScope, $scope, $location, $anchorScroll) {
        	// http://stackoverflow.com/questions/15033195/showing-spinner-gif-during-http-request-in-angular
			// To handle page reloads

        	if ($location.$$path.split('/')[1]) {
        		$scope.loading = true;
        	} else {
        		$scope.loading = false;
        	}

            $scope.sideToggle = function(outside){ 
                if(!outside)
                    $rootScope.sidebar = !$rootScope.sidebar;
                else
                    $rootScope.sidebar = false;
                
            };

            $scope.navigateTo = function(url,name,sideToggle){
                $location.path(url);
                $("#menuton span").text(name);
                console.log(sideToggle);
                if(sideToggle == "sideToggle")
                    $rootScope.sidebar = !$rootScope.sidebar;
            };

            $scope.scrollToTop = function(){
                $anchorScroll();
            }
        }]);