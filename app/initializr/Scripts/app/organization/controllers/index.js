angular
    .module('certificationtracker.organization.index', ['ngGrid', 'ui.select2'])
    .controller('organizationListController', [
        '$scope',
        '$location',
        '$http',
        'modalService',
		'organizationservice',
        function ($scope, $location, $http, modalService, organizationservice) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope(),
				_orderBy = 'Name',
        		_descending = false,
				_filterTimeout;
        	_spinner.loading = true;

            $scope.filterOptions = {
                filterText: "",
                useExternalFilter: false
            };

            $scope.totalServerItems = 0;
            
            $scope.pagingOptions = {
                pageSizes: [20, 50, 100],
                pageSize: 20,
                currentPage: 1
            };

            $scope.sortOptions = {
                fields: ["Name"],
                directions: ["asc"]
            };

            $scope.createOrganization = function () {
            	$location.path("/Organization/Create/");
            };

            $scope.detail = function detail(row) {
            	$location.path("/Organization/Detail/" + row.entity.OrganizationId);
            };

            $scope.remove = function (row) {
            	var modalOptions = {
            		closeButtonText: 'Cancel',
            		actionButtonText: 'Eliminar',
            		headerText: 'Eliminar Organización',
            		bodyText: '¿Desea eliminar la organización?'
            	};

            	modalService.showModal({}, modalOptions).then(function () {
            		_spinner.loading = true;

            		organizationservice
						.deleteOrganization(row.entity.OrganizationId)
						.success(function (data, status, headers, config) {
							$scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
						})
						.error(function (data, status, headers, config) {
							_spinner.loading = false;
						    showModal('Error', data, 'Cerrar');
						});
            	});
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
            
            $scope.getPagedDataAsync = function(pageSize, page, searchText) {
            	setTimeout(function () {
            		_spinner.loading = true;
                    var p = {
                        searchText: searchText,
                        pageNumber: $scope.pagingOptions.currentPage,
                        pageSize: $scope.pagingOptions.pageSize,
                        orderBy: _orderBy,
                        descending: _descending
                    };

                    $http({
                        url: "/api/organization/getall",
                        method: "GET",
                        params: p,
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

            $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);

            $scope.$watch('pagingOptions', function(newVal, oldVal) {
                if (newVal !== oldVal || newVal.currentPage !== oldVal.currentPage) {
                    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
                }
            }, true);

            $scope.$watch('filterOptions', function (newVal, oldVal) {
            	if (newVal !== oldVal) {
            		window.clearTimeout(_filterTimeout);
            		_filterTimeout = setTimeout(function () {
            			$scope.pagingOptions.currentPage = 1;
            			$scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
            		}, 500);
                }
            }, true);

            $scope.$watch('sortOptions', function (newVal, oldVal) {
                if (newVal.fields[0] != oldVal.fields[0] || newVal.directions[0] != oldVal.directions[0]) {
                    _orderBy = newVal.fields[0];
                    _descending = newVal.directions[0] !== "asc";
                    $scope.pagingOptions.currentPage = 1;
                    $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage, $scope.filterOptions.filterText);
                }
            }, true);

            $scope.$watch('search', function (newVal, oldVal) {
                if (newVal !== oldVal) {
                    if ($scope.filterOptions) {
                        $scope.filterOptions.filterText = newVal;
                    }
                }
            });
            
            $scope.gridOptions = {
                data: 'data',
                rowHeight: 50,
                headerRowHeight: 50,
                enablePaging: true,
                showFooter: true,
                multiSelect: false,
                totalServerItems: 'totalServerItems',
                pagingOptions: $scope.pagingOptions,
                filterOptions: $scope.filterOptions,
                sortInfo: $scope.sortOptions,
                useExternalSorting: true,
                columnDefs: [{ field: 'Name', displayName: 'Nombre', width: 550, groupable: false },
							{ field: 'Contacts', displayName: 'Contactos', width: 300, groupable: false },
                            {
                                displayName: '',
                                cellTemplate: '<button id="detailBtn" type="button" class="" ng-click="detail(row)" title="Detalle">C</button>' +
                                      '<button id="deleteBtn" type="button" class="" ng-click="remove(row)" title="Eliminar">E</button>',
                                width: 87,
                                headerClass: 'hidden',
                                groupable: false,
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
        }]);
