angular
    .module('certificationtracker.home', [])
    .controller('homeCtrl', [
        '$scope',
        '$location',
        function ($scope, $location) {
        	// http://stackoverflow.com/questions/15033195/showing-spinner-gif-during-http-request-in-angular
			// To handle page reloads
        	if ($location.$$path.split('/')[1]) {
        		$scope.loading = true;
        	} else {
        		$scope.loading = false;
        	}
        }]);