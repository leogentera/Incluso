// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('certificationtracker.exam.index', ['ui.select2', 'ngTagsInput'])
    .controller('examIndexController', [
        '$scope',
        '$location',
        '$routeParams',
        'examservice',
        'modalService',
		'$timeout',
		'$rootScope',
		'$http',
        function ($scope, $location, $routeParams, examservice, modalService, $timeout, $rootScope, $http) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope();
        	_spinner.loading = true;
        	$scope.totalServerItems = 0;

        	$scope.filterOptions = {
        		filterText: "",
        		useExternalFilter: false
        	};

        	$scope.returnToList = function () {
        		$location.path("/Calendar/Global");
        	};

        	$scope.add = function () {
        		$location.path("/Exam/Create");
        	};

        	$scope.edit = function (row) {
        		$location.path("/Exam/Edit/" + row.entity.ExamId);
        	};

        	$scope.remove = function (row) {
        		examservice
					.deleteExam(row.entity.ExamId)
					.success(function (data, status, headers, config) {
						$scope.data.splice(row.orig.rowIndex, 1);
					})
					.error(function (data, status, headers, config) {
						_spinner.loading = false;
						showModal('Error', data, 'Close');
					});
        	};

        	$scope.setData = function (data) {
        		$scope.data = data.Exams;
        		
        		if (!$scope.$$phase) {
        			$scope.$apply();
        		}
        	};

        	$scope.getDataAsync = function () {
        		setTimeout(function () {
        			$http({
        				url: "/api/exam/getall",
        				method: "GET",
        			}).success(function (largeLoad) {
        				$scope.setData(largeLoad);
        				_spinner.loading = false;
        			})
                    .error(function (data, status, headers, config) {
                    	_spinner.loading = false;
                    	showModal('Error', data, 'Close');
                    });
        		}, 100);
        	};

        	$scope.getDataAsync();

        	$scope.gridOptions = {
        		data: 'data',
        		rowHeight: 50,
        		headerRowHeight: 0,
        		showFooter: false,
        		enableSorting: false,
        		filterOptions: $scope.filterOptions,
        		multiSelect: false,
        		//showFilter: true,
        		totalServerItems: 'totalServerItems',
        		columnDefs: [{ field: 'ExamId', displayName: 'Exam Id', visible: false },
							{
								displayName: '',
								cellTemplate: '<div class="cell-status passed">S</div>',
								width: 10
							},
							{
								displayName: '',
								cellTemplate: '<span style="font-size: 13px;">{{row.entity.Code}}</span>',
								field: 'Code',
								width: 120
							},
							{
								displayName: '',
								cellTemplate: '<span style="font-size: 13px;">{{row.entity.Name}}</span>',
								field: 'Name',
								width: 710
							},
                            {
                            	displayName: '',
                            	cellTemplate: '<button id="deleteBtn" type="button" class="" ng-click="remove(row)" title="Delete">D</button>' +
									'<button id="detailBtn" type="button" class="" ng-click="edit(row)" title="Edit">E</button>',
                            	width: 70,
                            	headerClass: 'hidden',
                            	sortable: false
                            }]
        	};

        	$scope.$watch('search', function (newVal, oldVal) {
        		if (newVal !== oldVal) {
        			if ($scope.filterOptions) {
        				$scope.gridOptions.filterOptions.filterText = newVal;
        			}
        		}
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