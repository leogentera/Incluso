// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.programa.dashboard', [])
    .controller('programaDashboardController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
		'$timeout',
		'$rootScope',
		'$http',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http) {
            var _spinner = angular.element(document.getElementById('spinner')).scope();

            _spinner.loading = true;

            $scope.setData = function (data) {
                setTimeout(function () {
                    console.log('response length:' + data.length);
                    $scope.courses = data.toJSON();
                    $scope.course = _.findWhere($scope.courses, { sid: _courseId });
                    if (!$scope.$$phase) {
                        $scope.$apply();
                    }
                }, 1000);
            };

            $scope.getDataAsync = function () {

                console.log('getting courses');

                var courses = new models.Courses();

                    courses.fetch({
                       local: true,
                        success: function (data) {
                            console.log('courses are back');
                            _spinner.loading = false;
                            $scope.setData(data);

                        }
                    });
            };

            $scope.getDataAsync();

        }]);
