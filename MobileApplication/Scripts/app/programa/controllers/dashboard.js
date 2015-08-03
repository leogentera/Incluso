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

            $scope.Math = window.Math;

            getDataAsync();

            //$scope.setData = function (data) {
            //    setTimeout(function () {
            //        console.log('response length:' + data.length);
            //        $scope.courses = data.toJSON();
            //        $scope.course = _.findWhere($scope.courses, { sid: _courseId });
            //       if (!$scope.$$phase) {
            //            $scope.$apply();
            //        }
            //    }, 1000);
            //};

            //$scope.getDataAsync = function () {
            //
            //    console.log('getting courses');
            //
            //    var courses = new models.Courses();
            //
            //        courses.fetch({
            //           local: true,
            //            success: function (data) {
            //                console.log('courses are back');
            //                _spinner.loading = false;
            //                $scope.setData(data);
            //
            //            }
            //        });
            //};

            $scope.logout = function () {
                localStorage.removeItem("CurrentUser");
                $location.path('/');
            }

            function getDataAsync() {
                $scope.user = JSON.parse(localStorage.getItem("user"));
                $scope.course = JSON.parse(localStorage.getItem("course"));
                $scope.usercourse = JSON.parse(localStorage.getItem("course"));

                $scope.currentStage = getCurrentStage();
            }

            function getCurrentStage(){
                var currentStage = 1;

                return currentStage;

                for(var i = 0; i < $scope.usercourse.stages.length; i++){
                    var uc = $scope.usercourse.stages[i];

                    if(uc.status === 0){
                        return;
                    }

                    currentStage++;
                }

                
            }
        }]);
