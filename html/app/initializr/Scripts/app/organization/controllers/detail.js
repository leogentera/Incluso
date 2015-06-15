angular
    .module('certificationtracker.organization.detail', [])
    .controller('organizationDetailController', [
        '$scope',
        '$location',
        'organizationservice',
        '$routeParams',
        '$http',
        'modalService',
        function ($scope, $location, organizationservice, $routeParams, $http, modalService) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope(),
				_orderBy = "UserName",
        		_descending = false;
        	_spinner.loading = true;

        	$scope.organization = {
        		OrganizationId: -1,
        		Name: '',
        		IsActive: true,
        		SelectedAccounts: []
        	};

        	$scope.totalServerItems = 0;
        	
        	$scope.pagingOptions = {
        		pageSizes: [20, 50, 100],
        		pageSize: 20,
        		currentPage: 1
        	};

        	$scope.sortOptions = {
        		fields: ["UserName"],
        		directions: ["asc"]
        	};

        	$scope.returnToList = function () {
        		$location.path("/Organization/Index");
        	};

        	$scope.edit = function edit() {
        		$location.path("/Organization/Edit/" + $routeParams.id);
        	};

        	$scope.detail = function detail(row) {
        		$location.path("/Account/Detail/" + row.entity.AccountId);
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
        			if ($scope.organization.OrganizationId > 0) {
        				_spinner.loading = true;
        				var p = {
        					searchText: searchText,
        					pageNumber: $scope.pagingOptions.currentPage,
        					pageSize: $scope.pagingOptions.pageSize,
        					orderBy: _orderBy,
        					descending: _descending,
        					organizationId: $scope.organization.OrganizationId
        				};

        				$http({
        					url: "/api/account/getallbyorganizationid",
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
        			} else {
        				_spinner.loading = false;
        				showModal('Error', 'La organización es inválida.', 'Cerrar');
        			}
        		}, 100);
        	};

        	organizationservice
                    .readOrganization($routeParams.id || $scope.organization.OrganizationId, true)
                    .success(function (data, status, headers, config) {
                    	$scope.organization = data.Organization;
                        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);
                    })
					.error(function (data, status, headers, config) {
						_spinner.loading = false;
						showModal('Error', data, 'Cerrar');
					});

        	$scope.$watch('pagingOptions', function (newVal, oldVal) {
        	    if (newVal !== oldVal || newVal.currentPage !== oldVal.currentPage) {
        	        $scope.getPagedDataAsync($scope.pagingOptions.pageSize, $scope.pagingOptions.currentPage);
        	    }
        	}, true);

        	$scope.$watch('sortOptions', function (newVal, oldVal) {
        	    if (newVal.fields[0] != oldVal.fields[0] || newVal.directions[0] != oldVal.directions[0]) {
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
        		columnDefs: [{ field: 'UserName', displayName: 'Nombre de Usuario', groupable: false, width: 550 },
							{ field: 'RoleName', displayName: 'Rol', groupable: false, width: 300 },
                            {
                            	displayName: '',
                            	cellTemplate: '<button id="detailBtn" type="button" class="" ng-click="detail(row)" title="Detalle">C</button>',
                            	groupable: false,
                            	width: 87,
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
        }]);
