/* JS for navigation */


var app = angular.module('inlcuso.shared.mainNavigation', ['ui.bootstrap']);

app.controller('navController', function($scope){
	$scope.isNavCollapsed = true;
});

app.controller('menuController', [
	'$scope',
	'$location', 
	function($scope, $location){
		$scope.navigateTo = function(url){
                $location.path(url);
            };
}]);
 