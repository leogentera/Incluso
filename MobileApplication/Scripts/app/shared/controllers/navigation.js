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
 
 app.controller('menuOffCanvas',[
 	'$scope',
 	'$location',
 	function($scope, $location){

 		$scope.sideToggle = function(action){ 	
 		
 			if(action == 'in')
 				$("body").addClass("sidebar-left-visible sidebar-left-in");
 			else
 				$("body").removeClass("sidebar-left-visible sidebar-left-in");
 		};

 		$scope.sideToggleOut = function () {
 			console.log("out");
 			$("body").removeClass("sidebar-left-visible sidebar-left-in");
 		};
 }]);