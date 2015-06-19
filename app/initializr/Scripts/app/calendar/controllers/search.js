angular
    .module('certificationtracker.calendar.search', ['autocomplete'])
    .controller('calendarSearchController', [
        '$scope',
        '$location',
        'calendarservice',
        '$routeParams',
		'modalService',
		'$rootScope',
		'$timeout',
        function ($scope, $location, calendarservice, $routeParams, modalService, $rootScope, $timeout) {
            var _spinner = angular.element(document.getElementById('spinner')).scope(), _filterTimeout;
            _spinner.loading = false;
            $scope.data = [];
            $scope.personName = '';

            $scope.returnToList = function () {
                $location.path("/Calendar/Global");
            };

            $scope.add = function () {
                $location.path("/Calendar/Create");
            };

            $scope.edit = function (row) {
                $location.path("/Calendar/Edit/" + row.entity.CalendarId);
            };

            $scope.remove = function (row) {
                calendarservice
					.deleteExam(row.entity.CalendarId)
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
                $scope.personName = data.Name;
                $scope.isEditable = data.IsEditable;

                // Update ngGrid
                if (!$scope.$$phase) {
                    $scope.$apply();
                }
            };

            $scope.getUsers = function (name) {
                if (name) {
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

            $scope.search = function () {
                var username = $scope.calendarForm.username.$viewValue;

                if (username) {
                    _spinner.loading = true;

                    calendarservice
						.getPersonByName(username)
						.success(function (data, status, headers, config) {
						    $scope.setData(data);
						    _spinner.loading = false;
						})
						.error(function (data, status, headers, config) {
						    _spinner.loading = false;
						    showModal('Error', data, 'Close');
						});
                }
            };

            $scope.gridOptions = {
                data: 'data',
                rowHeight: 50,
                headerRowHeight: 0,
                showFooter: false,
                enableSorting: false,
                multiSelect: false,
                totalServerItems: 'totalServerItems',
                columnDefs: [{ field: 'CalendarId', displayName: 'Calendar Id', visible: false },
							{
							    displayName: '',
							    cellTemplate: '<div class="cell-status" ng-class="{passed: (row.entity.StatusId == 1), scheduled: (row.entity.StatusId == 2), pending: (row.entity.StatusId == 3)}">S</div>',
							    width: 10
							},
							{
							    displayName: '',
							    cellTemplate: '<span style="font-size: 13px; cursor: default; color: #333333">{{row.entity.ExamName}}</span>',
							    width: 680
							},
							{
							    displayName: '',
							    cellTemplate: '<span style="font-size: 13px; cursor: default; color: #333333">{{row.entity.ScheduleDateRaw}}</span>',
							    width: 150
							},
                            {
                                displayName: '',
                                cellTemplate: '<button id="deleteBtn" type="button" class="" ng-click="remove(row)" ng-show="row.entity.StatusId == 3 && isEditable" title="Delete">C</button>' +
									'<button id="detailBtn" type="button" class="" ng-click="edit(row)" ng-show="isEditable" title="Edit">D</button>',
                                width: 70,
                                headerClass: 'hidden',
                                sortable: false
                            }]
            };

            calendarservice
				.getPersonById($routeParams.id || -1)
				.success(function (data, status, headers, config) {
				    $scope.setData(data);
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