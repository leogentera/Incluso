angular
    .module('certificationtracker.calendar.index', ['ngGrid'])
    .controller('calendarIndexController', [
        '$scope',
        '$location',
        '$http',
        'modalService',
		'$rootScope',
        function ($scope, $location, $http, modalService, $rootScope) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope();
        	_spinner.loading = true;

        	$scope.setData = function (data) {
        		$scope.data = data.Statuses;
        		var container = $('#canvas')[0].getContext("2d");

        		new Chart(container).Pie([{
						value: $scope.data[0].Value,
						color: '#227A28',
						label: 'Passed', segmentStrokeWidth: 8,
						labelColor: 'white',
						labelFontSize: '10',
						labelAlign: 'left'
					}, {
						value: $scope.data[1].Value,
						color: '#FEC957',
						label: 'Scheduled'
					}, {
						value: $scope.data[2].Value,
						color: "#E0262F",
						label: 'FHJFJHFFFFFFFFFFF',
						labelColor: 'black',
						labelFontSize: '16'
					}], {
						// Whether we should show a stroke on each segment
						segmentShowStroke: true,
						// The colour of each segment stroke
						segmentStrokeColor: "#fff",
						// The width of each segment stroke
						segmentStrokeWidth: 8,
						// Amount of animation steps
						animationSteps: 50,
						// Animation easing effect
						animationEasing: "easeOutQuart",
						showTooltips: false
					});
        	};

        	$scope.getDataAsync = function () {
        		setTimeout(function () {
        			$http({
        				url: "/api/calendar/getall",
        				method: "GET",
        			}).success(function (largeLoad) {
        				$scope.setData(largeLoad);
        				_spinner.loading = false;

        				$rootScope.pagingTimeout = setTimeout(function () {
        					$location.path("/Calendar/Weekly");

        					if (!$scope.$$phase) {
        						$scope.$apply();
        					}
        				}, 10000);
        			})
                    .error(function (data, status, headers, config) {
                    	_spinner.loading = false;
                    	showModal('Error', data, 'Close');
                    });
        		}, 100);
        	};

        	$scope.getDataAsync();


        	function showModal(headerText, message, actionText) {
        		var modalOptions = {
        			showCloseButton: false,
        			actionButtonText: actionText,
        			headerText: headerText,
        			bodyText: message
        		};

        		modalService.showModal({}, modalOptions);
        	};
        }]);