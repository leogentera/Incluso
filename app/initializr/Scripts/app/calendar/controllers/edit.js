angular
    .module('certificationtracker.calendar.edit', ['ui.select2', 'autocomplete'])
    .controller('calendarEditController', [
        '$scope',
        '$location',
        'calendarservice',
        '$routeParams',
		'modalService',
		'$rootScope',
		'$timeout',
        function ($scope, $location, calendarservice, $routeParams, modalService, $rootScope, $timeout) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope(), _filterTimeout;
        	_spinner.loading = true;
        	$scope.isNew = angular.isUndefined($routeParams.id);
        	$scope.users = [];
        	$scope.exams = [];
        	$scope.statuses = [];
        	var _statusEnum = {
        		Passed: 1,
        		Scheduled: 2,
				Pending: 3
        	};

        	$scope.calendar = {
				CalendarId: -1,
        		PersonId: -1,
        		PersonName: '',
        		ExamId: 1,
        		StatusId: _statusEnum.Pending,
        		ScheduleDate: ''
        	};

        	$scope.openScheduledDate = function () {
        		$timeout(function () {
        			$scope.scheduledDateOpened = true;
        		});
        	};

        	$scope.returnToList = function () {
        		$location.path("/Calendar/Search");
        	};

        	$scope.submitForm = function () {
        		$scope.calendarForm.submitted = true;
        	};

        	$scope.getUsers = function (name) {
        		if (name && $scope.isNew) {
        			window.clearTimeout(_filterTimeout);
        			_filterTimeout = setTimeout(function () {
        				calendarservice
							.getDomainUsers(name)
							.success(function (data, status, headers, config) {
								$scope.users = data;
							});
        			}, 500);
        		}
        	};

        	$scope.save = function () {
        		if (isValidForm()) {
        			_spinner.loading = true;

        			if ($scope.isNew) {
        				$scope.calendar.PersonName = $scope.calendarForm.username.$viewValue;

        				calendarservice
							.createCalendar($scope.calendar)
							.success(function (data, status, headers, config) {
								showModal('Saved', 'The exam has been saved.', 'Close');
								$scope.calendar.ExamId = 1;
								$scope.calendar.StatusId = _statusEnum.Pending;
								$scope.calendar.ScheduleDate = '';

								$('#select-exam').select2('val', $scope.calendar.ExamId);
								$('#select-status').select2('val', $scope.calendar.StatusId);

								_spinner.loading = false;
							})
							.error(function (data, status, headers, config) {
								_spinner.loading = false;
								showModal('Error', data, 'Close');
							});
        			} else {
        				calendarservice
							.updateCalendar($scope.calendar)
							.success(function (data, status, headers, config) {
								showModal('Saved', 'The exam has been saved.', 'Close');
								$location.path("/Calendar/Search");
							})
							.error(function (data, status, headers, config) {
								_spinner.loading = false;
								showModal('Error', data, 'Close');
							});
        			}
        		}
        	};

            calendarservice
				.readCalendar($routeParams.id || $scope.calendar.CalendarId, false)
				.success(function (data, status, headers, config) {
					if (data.Calendar) {
						$scope.calendar = data.Calendar;
						$rootScope.userNameReadOnly = true;
						$("[name='username']").val(data.Calendar.PersonName);
						$scope.calendarForm.username.$setViewValue(data.Calendar.PersonName);
					}

					$scope.exams = data.Exams;
					$scope.statuses = data.Statuses;
					_spinner.loading = false;
				})
				.error(function (data, status, headers, config) {
					_spinner.loading = false;
				    showModal('Error', data, 'Close');
				});

            $scope.$watch('calendar.StatusId', function (newVal, oldVal) {
            	if (newVal !== oldVal) {
            		if (newVal == _statusEnum.Pending) {
            			$scope.calendar.ScheduleDate = '';
            		}
            	}
            }, true);

            function showModal(headerText, message, actionText) {
            	var modalOptions = {
            		showCloseButton: false,
            		actionButtonText: actionText,
            		headerText: headerText,
            		bodyText: message
            	};

            	modalService.showModal({}, modalOptions);
            };

            function isValidForm() {
            	var isValidStatus = true;

            	if ($scope.calendar.StatusId == _statusEnum.Passed || $scope.calendar.StatusId == _statusEnum.Scheduled) {
            		if ($scope.calendar.ExamId == 1 || !$scope.calendar.ScheduleDate) {
            			isValidStatus = false;
            			showModal('Validation error', 'Exam and date must be specified.', 'Close');
            		}
            	}

            	return $scope.calendarForm.$valid && $scope.calendarForm.scheduledDate.$valid && isValidStatus;
            };

        	// This helps to clear the timeout from Global, Index and Weekly and avoid change of page
            if ($rootScope.pagingTimeout) {
            	window.clearTimeout($rootScope.pagingTimeout);
            }
        }]);