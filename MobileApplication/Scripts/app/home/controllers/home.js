angular
    .module('incluso.home', [])
    .controller('homeCtrl', [
        '$rootScope',
        '$scope',
        '$location',
        '$anchorScroll',
        '$window',
        function ($rootScope, $scope, $location, $anchorScroll, $window ) {
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

            $scope.navigateTo = function(url,name,sideToggle,navbarColor){
                $location.path(url);
                if(navbarColor == 'blue')
                    $rootScope.navbarBlue = true;
                if(navbarColor == 'orange')
                    $rootScope.navbarOrange = true;
                $("#menuton span").text(name);
                
                if(sideToggle == "sideToggle")
                    $rootScope.sidebar = !$rootScope.sidebar;
            };

            $scope.scrollToTop = function(element){
                
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $location.$hash(element);
                $anchorScroll();
                console.log($location.$$hash);
            }
        }]);