angular
    .module('certificationtracker.report.updatecontactreport', ['ui.multiselect', 'ngGrid', 'ngSanitize'])
    .controller('updateContactReportController', [
        '$scope',
        'reportservice',
        '$timeout',
        '$http',
        'modalService',
        function ($scope, reportservice, $timeout, $http, modalService) {
            // Hiding submenu on click
        	$("#menu #submenu").addClass('unhovered');
        	var _spinner = angular.element(document.getElementById('spinner')).scope(),
				_orderBy = 'Date',
				_descending = true;
        	_spinner.loading = false;

            $scope.totalServerItems = 0;

            $scope.openInitDate = function () {
                $timeout(function () {
                    $scope.initDateOpened = true;
                });
            };

            $scope.openEndDate = function () {
                $timeout(function () {
                    $scope.endDateOpened = true;
                });
            };

            $scope.pagingOptions = {
                pageSizes: [20, 50, 100],
                pageSize: 20,
                currentPage: 1
            };

            $scope.modified = function (row) {
                _spinner.loading = true;

                reportservice
                   .informationModifiedReport(row.entity.ActivityLogId)
                   .success(function (data, status, headers, config) {
                       _spinner.loading = false;
                       modalService.showModalCustom({
                           templateUrl: 'updateInfo',
                           backdrop: 'static',
                           windowClass: 'modal',
                           controller: ['$scope', '$modalInstance', 'message',
                               function ($scope, $modalInstance, message) {
                                   $scope.message = message;

                                   $scope.close = function () {
                                       $modalInstance.dismiss('cancel');
                                   };
                               }],
                           resolve: {
                               message: function () {
                                   return data.BodyText;
                               }
                           }
                       });
                   })
                .error(function (data, status, headers, config) {
                    _spinner.loading = false;
                    showModal('Error', data, 'Cerrar');
                });
            };

            $scope.generateReport = function () {
            	if ($scope.reportForm.$valid) {
            		_spinner.loading = true;
            		$scope.pagingOptions.currentPage = 1;

                    var report = {
                        initDate: $scope.report.initDate,
                        endDate: $scope.report.endDate,
                        pageNumber: $scope.pagingOptions.currentPage,
                        pageSize: $scope.pagingOptions.pageSize,
                        orderBy: 'Date'
                    };

                    reportservice
                        .generateCreateOrUpdateReport(report)
                        .success(function (data, status, headers, config) {
                        	$scope.setPagingData(data);
                            _spinner.loading = false;
                        })
						.error(function (data, status, headers, config) {
							_spinner.loading = false;
							showModal('Error', data, 'Cerrar');
						});
                } else {
                	$scope.reportForm.submitted = true;
                }
            };

            $scope.sortOptions = {
            	fields: ['Date'],
            	directions: ['desc']
            };

            $scope.setPagingData = function (data, page, pageSize) {
                if (data) {
                    $scope.totalServerItems = data.Total;
                    $scope.data = data.Rows;
                    if (!$scope.$$phase) {
                        $scope.$apply();
                    }
                }
            };

            $scope.getPagedDataAsync = function (pageSize, page, searchText) {
            	setTimeout(function () {
            		_spinner.loading = true;
                    var p = {
                        initDate: $scope.report.initDate,
                        endDate: $scope.report.endDate,
                        pageNumber: $scope.pagingOptions.currentPage,
                        pageSize: $scope.pagingOptions.pageSize,
                        orderBy: _orderBy,
                        descending: _descending
                    };

                    $http({
                        url: "/api/report/generatecreateorupdatereport",
                        method: "POST",
                        data: p,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    }).success(function (largeLoad) {
                        $scope.setPagingData(largeLoad, page, pageSize);
                        _spinner.loading = false;
                    })
					.error(function (data, status, headers, config) {
						_spinner.loading = false;
					    showModal('Error', data, 'Cerrar');
					});
                }, 100);
            };

            $scope.$watch('pagingOptions', function (newVal, oldVal) {
            	if ((newVal !== oldVal || newVal.currentPage !== oldVal.currentPage) && $scope.reportForm.$valid) {
                    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);
                }
            }, true);

            $scope.$watch('sortOptions', function (newVal, oldVal) {
            	if ((newVal.fields[0] != oldVal.fields[0] || newVal.directions[0] != oldVal.directions[0]) && $scope.reportForm.$valid) {
                    _orderBy = newVal.fields[0];
                    _descending = newVal.directions[0] !== "asc";
                    $scope.pagingOptions.currentPage = 1;
                    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);
                }
            }, true);

            $scope.gridOptions = {
                data: 'data',
                rowHeight: 50,
                headerRowHeight: 50,
                enablePaging: true,
                showFooter: true,
                multiSelect: false,
                totalServerItems: 'totalServerItems',
                pagingOptions: $scope.pagingOptions,
                sortInfo: $scope.sortOptions,
                useExternalSorting: true,
                columnDefs: [{ field: 'AccountName', displayName: 'Nombre del Contacto', width: 200, groupable: false },
                             { field: 'ActivityName', displayName: 'Acción', cellTemplate: '<div class="ngCellText col9 colt9"><p ng-cell-text style="cursor: default;white-space: pre-line;">{{row.getProperty(col.field)}}</p></div>', width: 200, groupable: false },
                             { field: 'UserName', displayName: 'Modificación Por', width: 200, groupable: false },
                             { field: 'Date', displayName: 'Fecha', width: 200, groupable: false },
                             {
                             	displayName: '',
                             	cellTemplate: '<button id="detailBtn" type="button" class="" ng-click="modified(row)" ng-show="row.entity.IsModified" title="Detalle">D</button>',
                                sortable: false,
                                width: 80, 
                                headerClass: 'hidden' }]
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

        }]);
