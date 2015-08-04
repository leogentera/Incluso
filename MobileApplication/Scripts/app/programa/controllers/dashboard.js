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

            $scope.user = JSON.parse(moodleFactory.Services.GetCacheObject("profile"));
            $scope.usercourse = JSON.parse(moodleFactory.Services.GetCacheObject("usercourse"));
            $scope.course = JSON.parse(moodleFactory.Services.GetCacheObject("course"));
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
            };

            $scope.navigateToStage = function(){
                $location.path('/ProgramaDashboardEtapa/' + $scope.stage.stageId);
            };

            function getDataAsync() {
                //$scope.course = JSON.parse(localStorage.getItem("course"));
                //$scope.usercourse = JSON.parse(localStorage.getItem("usercourse"));

                $scope.currentStage = getCurrentStage();
            }

            function getCurrentStage(){
                var currentStage = 1;

                for(var i = 0; i < $scope.usercourse.stages.length; i++){
                    var uc = $scope.usercourse.stages[i];

                    $scope.stage = uc;
                    if(uc.stageStatus === 0){
                        break;
                    }

                    currentStage++;
                }

                return currentStage;
            }
        }]);
