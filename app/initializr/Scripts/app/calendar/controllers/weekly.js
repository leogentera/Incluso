// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('certificationtracker.calendar.weekly', ['ui.select2', 'ngTagsInput'])
    .controller('calendarWeeklyController', [
        '$scope',
        '$location',
        '$routeParams',
        'modalService',
		'$timeout',
		'$rootScope',
		'$http',
        function ($scope, $location, $routeParams, modalService, $timeout, $rootScope, $http) {
            var _spinner = angular.element(document.getElementById('spinner')).scope(),
				_page = 1, _pageSize = 17;

            _spinner.loading = true;
            $scope.totalServerItems = 0;

            $scope.detail = function (id) {
                $location.path("/Calendar/Detail/" + id);
            };

            $scope.search = function (row) {
                $location.path("/Calendar/Search/" + row.entity.PersonId);
            };

            $scope.setData = function (data) {
                $scope.persons = data.Persons;
                pagingInfo();
            };

            $scope.getDataAsync = function () {
                setTimeout(function () {
                    $http({
                        url: "/api/calendar/getweekly",
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
                multiSelect: false,
                totalServerItems: 'totalServerItems',
                columnDefs: [{ field: 'PersonId', displayName: 'Person Id', visible: false },
							{
							    field: 'Name',
							    displayName: '',
							    cellTemplate: '<span ng-click="search(row)">{{row.entity.Name}}</span>',
							    width: 400
							},
                            {
                                displayName: '',
                                cellTemplate: '<span ng-repeat="item in row.entity.Exams">' +
									'<button type="button" class="square" ng-class="{passed: (item.StatusId == 1), scheduled: (item.StatusId == 2), pending: (item.StatusId == 3)}" ng-click="detail(item.CalendarId)" title="Editar">E</button></span>',
                                width: 538,
                                headerClass: 'hidden',
                                sortable: false
                            }]
            };

            function showModal(headerText, message, actionText) {
                var modalOptions = {
                    showCloseButton: false,
                    actionButtonText: actionText,
                    headerText: headerText,
                    bodyText: message
                };

                modalService.showModal({}, modalOptions);
            };

            function pagingInfo() {
                var firstResult = (_page - 1) * _pageSize,
					maxResult = _page * _pageSize,
					total = $scope.persons.length;

                if (firstResult < total) {
                    $scope.data = $scope.persons.slice(firstResult, maxResult);
                    _page++;

                    // Clear the timeout
                    if ($rootScope.pagingTimeout) {
                        window.clearTimeout($rootScope.pagingTimeout);
                    }

                    $rootScope.pagingTimeout = setTimeout(pagingInfo, 10000);
                } else {
                    $location.path("/Calendar/Global");
                }

                // Update ngGrid
                if (!$scope.$$phase) {
                    $scope.$apply();
                }
            };
        }]);