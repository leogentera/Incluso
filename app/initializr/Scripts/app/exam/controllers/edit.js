angular
    .module('certificationtracker.exam.edit', [])
    .controller('examEditController', [
        '$scope',
        '$location',
        'examservice',
        '$routeParams',
		'modalService',
		'$rootScope',
		'$timeout',
        function ($scope, $location, examservice, $routeParams, modalService, $rootScope, $timeout) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope();
        	_spinner.loading = true;
        	$scope.isNew = angular.isUndefined($routeParams.id);

        	$scope.exam = {
        		ExamId: -1,
        		Code: '',
        		Name: '',
        		FullName: ''
        	};

        	$scope.returnToList = function () {
        		$location.path("/Exam/Index");
        	};

        	$scope.submitForm = function () {
        		$scope.examForm.submitted = true;
        	};

        	$scope.save = function () {
        		if ($scope.examForm.$valid) {
        			_spinner.loading = true;

        			if ($scope.isNew) {
        				examservice
							.createExam($scope.exam)
							.success(function (data, status, headers, config) {
								showModal('Saved', 'The exam has been saved.', 'Close');
								$scope.exam.Code = '';
								$scope.exam.Name = '';
								$scope.examForm.submitted = false;
								_spinner.loading = false;
							})
							.error(function (data, status, headers, config) {
								_spinner.loading = false;
								showModal('Error', data, 'Close');
							});
        			} else {
        				examservice
							.updateExam($scope.exam)
							.success(function (data, status, headers, config) {
								showModal('Saved', 'The exam has been saved.', 'Close');
								$location.path("/Exam/Index");
							})
							.error(function (data, status, headers, config) {
								_spinner.loading = false;
								showModal('Error', data, 'Close');
							});
        			}
        		}
        	};

            examservice
				.readExam($routeParams.id || $scope.exam.ExamId, false)
				.success(function (data, status, headers, config) {
					if (data.ExamId) {
						$scope.exam = data;
					}

					_spinner.loading = false;
				})
				.error(function (data, status, headers, config) {
					_spinner.loading = false;
				    showModal('Error', data, 'Close');
				});

            function showModal(headerText, message, actionText) {
            	var modalOptions = {
            		showCloseButton: false,
            		actionButtonText: actionText,
            		headerText: headerText,
            		bodyText: message
            	};

            	modalService.showModal({}, modalOptions);
            };

        	// This helps to clear the timeout from Global, Index and Weekly and avoid change of page
            if ($rootScope.pagingTimeout) {
            	window.clearTimeout($rootScope.pagingTimeout);
            }
        }]);