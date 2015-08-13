/* JS for navigation */


var app = angular.module('inlcuso.shared.mainNavigation', ['ui.bootstrap']);

app.controller('navController', function($scope){
	$scope.isNavCollapsed = true;
});

app.controller('menuController', [
	'$scope',
	'$location', 
	function($scope, $location, $http){

		$(".accsub").unbind("click");
		$(".accsub").bind("click",function(e){
			e.stopPropagation();
			$(this).toggleClass('icon-arrow');
			$(this).toggleClass('icon-arrow-up');
			$(this).toggleClass('green');
			$(this).toggleClass('white');
			var cual = $(this).attr('data-id');
			  $('#sub' + cual).toggle();

		});
		$scope.navigateTo = function(url,name){
            	$("body").removeClass("sidebar-left-visible sidebar-left-in");
            	$location.path(url);
            	$("#menuton span").text(name);
            };

            $scope.logout = function(){
            	$("body").removeClass("sidebar-left-visible sidebar-left-in");
                logout($http, $scope, $location);
            }; 
}]);
 
 app.controller('menuOffCanvas',[
 	'$scope',
 	'$location',
 	function($scope, $location){

 		$scope.sideToggle = function(action){ 
 			
 			if(action == 'toggle')
				$("body").toggleClass("sidebar-left-visible sidebar-left-in");
 			else if(action == 'in')
 				$("body").addClass("sidebar-left-visible sidebar-left-in");
 			else
 				$("body").removeClass("sidebar-left-visible sidebar-left-in");
 		};

 		$scope.sideToggleOut = function () {
 			$("body").removeClass("sidebar-left-visible sidebar-left-in");
 		};
 }]);