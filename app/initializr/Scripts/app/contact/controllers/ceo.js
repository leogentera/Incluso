angular
    .module('certificationtracker.contact.ceo', ['ngGrid'])
    .controller('contactCeoController', [
        '$scope',
        '$location',
        '$http',
        'modalService',
		'contactservice',
        function ($scope, $location, $http, modalService, contactservice) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope();
        	_spinner.loading = true;

            $scope.filterOptions = {
                filterText: "",
                useExternalFilter: false
            };
            
            $scope.totalServerItems = 0;
            $scope.isCeo = true;

            $scope.detail = function (row) {
            	$location.path("/Contact/Detail/" + row.entity.ContactId);
            };

            $scope.sync = function () {
            	var selectedContacts = getSelectedContacts();
            	
                _spinner.loading = true;

                contactservice
				    .syncContactsCeo(selectedContacts)
				    .success(function (data) {
					    _spinner.loading = false;
					    showModal('Guardar', 'Los contactos seleccionados se han guardado y se sincronizarán con los contactos de Exchange.', 'Cerrar');
				    })
				    .error(function (data, status, headers, config) {
					    _spinner.loading = false;
					    showModal('Error', data, 'Cerrar');
				    });
            };

            $scope.exportContacts = function () {
                var wind = $(window);
                var sizeH = wind.height() - 150;
                // 853 is the width of the grid (after removing the last column) and we add 50 pixels for padding in the dialog. 200 is a padding from the border of the browser to the border of the dialog
                var sizeW = (wind.width() - 200 > 903) ? 903 : wind.width() - 200;

                modalService.showModalCustomWithSize({
                    templateUrl: 'modalContacts',
                    backdrop: 'static',
                    // we pass the width and height from this way because other solutions didn't work. we would need to dive into how angular and its directives work in order to implement it in a different way
                    winh: sizeH,
                    winw: sizeW,
                    windowClass: 'modal contacts-dialog',
                    controller: ['$scope', '$modalInstance', 'contactservice', 'contacts',
						function ($scope, $modalInstance, contactservice, contacts) {
						    $scope.exportGridOptions = {
						        data: contacts,
						        rowHeight: 50,
						        headerRowHeight: 50,
						        showFooter: false,
						        multiSelect: true,
						        totalServerItems: 'totalServerItems',
						        aggregateTemplate: '<div ng-click="row.toggleExpand()" ng-style="rowStyle(row)" class="ngAggregate"><div class="{{row.aggClass()}}"></div><div class="ngAggCheck"><input type="checkbox" ng-click="toggleCheck(row)" ng-checked="row.aggrselected" ></div><div class="ngAggSpan"><span class="ngAggregateText">{{row.label CUSTOM_FILTERS}}</span></div></div>',
						        showGroupPanel: false,
						        groupsCollapsedByDefault: true,
						        showSelectionCheckbox: true,
						        showFilter: false,
						        // We use groupable=true to indicate that it will be used in grouping
						        sortInfo: { fields: ['Organization', 'Status', 'FullName'], directions: ['asc', 'asc', 'asc'] },
						        columnDefs: [{ field: 'ContactId', displayName: 'Contact Id', visible: false },
											{ field: 'OrganizationId', displayName: 'OrganizationId', visible: false },
											{ field: 'Organization', displayName: 'Organization', visible: false },
											{ field: 'FullName', displayName: 'Nombre de Contactos', width: 300 },
											{ field: 'Modified', displayName: 'Actualización', width: 300 },
											{ field: 'Status', displayName: 'Estatus', cellTemplate: '<div class="ngCellText col9 colt9" ng-class="{red: (row.entity.StatusId != 0), green: (row.entity.StatusId == 0)}"><span ng-cell-text>{{row.getProperty(col.field)}}</span></div>', width: 200, headerClass: 'hidden' },
						        ],
						        groups: ['Organization']
						    };

						    $scope.ok = function () {
						        var selectedContacts = getSelectedContactsToExport();

						        if (selectedContacts.length) {
						            _spinner.loading = true;
						            var toExport = {
						                contacts: selectedContacts,
						                organizationId: -1
						            };

						            contactservice
						                .exportContacts(toExport)
						                .success(function (data, status, headers, config) {
						                    var fileName = data;
						                    _spinner.loading = false;
						                    showModal('Exportar Contactos', 'Se han exportado los contactos seleccionados.', 'Cerrar');
						                    $('#hidden-excel-form').remove();

						                    $('<form>').attr({
						                        method: 'POST',
						                        id: 'hidden-excel-form',
						                        action: '/api/export/getfile/' + fileName
						                    }).appendTo('body');

						                    // We wait to show the modal and after that we submit the form.
						                    // If we dont do this the modal isn't displayed
						                    setTimeout(function () {
						                        $('#hidden-excel-form').submit();
						                        // After get the file we delete it
						                        setTimeout(function () {
						                            $http({
						                                method: 'DELETE',
						                                url: '/api/export/deletefile/' + fileName,
						                                headers: { 'RequestVerificationToken': $("#token").text() }
						                            });

						                            // Close the modal window
						                            $modalInstance.dismiss('cancel');
						                        }, 10);
						                    }, 500);
						                })
						                .error(function (data, status, headers, config) {
						                    _spinner.loading = false;
						                    showModal('Error', data, 'Cerrar');
						                });
						        }
						        else {
						            showModal('Error', 'No ha seleccionado contactos.', 'Cerrar');
						        }
						    };

						    $scope.close = function () {
						        $modalInstance.dismiss('cancel');
						    };

						    // Toggle check in modal of export contacts
						    $scope.toggleCheck = function (row) {
						        var isChecked = row.elm.find("input").attr("checked");
						        row.elm.find("input").attr("checked", isChecked);
						        // Setting parent in memory
						        var currentAggregate = row.aggIndex;
						        var total2 = $scope.exportGridOptions.ngGrid.rowFactory.parsedData.length;
						        var currentIndex = 0;
						        var currentCount = 0;
						        // Checking the parent while there are elements in the grid
						        while (currentIndex < total2) {
						            // Check if we found an aggregate
						            if ($scope.exportGridOptions.ngGrid.rowFactory.parsedData[currentIndex].isAggRow) {
						                // If we reached the aggregate we were looking for (by index) then check it
						                if (currentAggregate == currentCount) {
						                    $scope.exportGridOptions.ngGrid.rowFactory.parsedData[currentIndex].aggrselected = !isChecked;
						                    break;
						                }
						                else {
						                    currentCount++;
						                }
						            }
						            currentIndex++;
						        }
						        // Checking children in memory and DOM
						        var total = row.children.length;
						        for (var i = 0; i < total; i++) {
						            row.children[i].entity.Selected = !isChecked;
						        }
						        // If we don't do this, the checkboxes of the children don't change
						        row.notifyChildren();
						    };

						    function getSelectedContactsToExport() {
						        var rows = $scope.exportGridOptions.ngGrid.rowFactory.parsedData.length,
										selected = [],
										count = 0;

						        for (var i = rows; i--;) {
						            var row = $scope.exportGridOptions.ngGrid.rowFactory.parsedData[i];
						            if (!row.isAggRow && row.entity.Selected && row.entity.IsSelectable) {
						                selected[count++] = { ContactId: row.entity.ContactId };
						            }
						        }

						        return selected;
						    };
						}],
                    resolve: {
                        contacts: function () {
                            // Copy contacts retrieved from server
                            var contacts = $.map($scope.data, function (item) {
                                return {
                                    ContactId: item.ContactId,
                                    FullName: item.FullName,
                                    Index: item.Index,
                                    IsEditable: item.IsEditable,
                                    IsSelectable: item.IsSelectable,
                                    Modified: item.Modified,
                                    ModifiedDate: item.ModifiedDate,
                                    Organization: item.Organization,
                                    OrganizationId: item.OrganizationId,
                                    Selected: false,
                                    Status: item.Status,
                                    StatusId: item.StatusId
                                };
                            });

                            return contacts;
                        }
                    }
                });
            };

            $scope.toggleCheck = function (row) {
            	var isChecked = row.elm.find("input").attr("checked");
            	row.elm.find("input").attr("checked", isChecked);
            	// Setting parent in memory
            	var currentAggregate = row.aggIndex;
            	var total2 = $scope.gridOptions.ngGrid.rowFactory.parsedData.length;
            	var currentIndex = 0;
            	var currentCount = 0;
            	// Checking the parent while there are elements in the grid
            	while (currentIndex < total2) {
            		// Check if we found an aggregate
            		if ($scope.gridOptions.ngGrid.rowFactory.parsedData[currentIndex].isAggRow) {
            			// If we reached the aggregate we were looking for (by index) then check it
            			if (currentAggregate == currentCount) {
            				$scope.gridOptions.ngGrid.rowFactory.parsedData[currentIndex].aggrselected = !isChecked;
            				break;
            			}
            			else {
            				currentCount++;
            			}
            		}
            		currentIndex++;
            	}
            	// Checking children in memory and DOM
            	var total = row.children.length;
            	for (var i = 0; i < total; i++) {
            		row.children[i].entity.Selected = !isChecked;
            	}
                // If we don't do this, the checkboxes of the children don't change
                row.notifyChildren();
            };

            $scope.setData = function (data) {
            	$scope.data = data.Contacts;
            	$scope.organizationsList = data.Organizations;
            	$scope.userCanAdd = data.UserCanAddContact;
            	$scope.userCanMove = data.UserCanMoveContacts;
            	$scope.userCanRequestUpdate = data.UserCanRequestUpdate;
                if (!$scope.$$phase) {
                    $scope.$apply();
                }
            };

            $scope.getDataAsync = function (pageSize, page, searchText) {
                setTimeout(function() {
                    var p = {
                        name: $scope.filterOptions.filterText
                    };

                    $http({
                        url: "/api/contact/getallceo",
                        method: "GET",
                        params: p,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    }).success(function(largeLoad) {
                        $scope.setData(largeLoad, page, pageSize);
                        _spinner.loading = false;
                    })
                    .error(function (data, status, headers, config) {
                    	_spinner.loading = false;
                        showModal('Error', data, 'Cerrar');
                    });
                }, 100);
            };

            $scope.getDataAsync();
			
            $scope.$watch('search', function (newVal, oldVal) {
            	if (newVal !== oldVal) {
            		if ($scope.filterOptions) {
            			$scope.gridOptions.filterOptions.filterText = newVal;
            		}
            	}
            });

            $scope.gridOptions = {
                data: 'data',
                rowHeight: 50,
                headerRowHeight: 50,
                showFooter: false,
                multiSelect: true,
                totalServerItems: 'totalServerItems',
                filterOptions: $scope.filterOptions,
                aggregateTemplate: '<div ng-click="row.toggleExpand()" ng-style="rowStyle(row)" class="ngAggregate"><div class="{{row.aggClass()}}"></div><div class="ngAggCheck"><input type="checkbox" ng-click="toggleCheck(row)" ng-checked="row.aggrselected" ></div><div class="ngAggSpan"><span class="ngAggregateText">{{row.label CUSTOM_FILTERS}}</span></div></div>',
                showGroupPanel: false,
                groupsCollapsedByDefault: true,
                showSelectionCheckbox: true,
                showFilter: true,
                // We use groupable=true to indicate that it will be used in grouping
                sortInfo: { fields: ['Organization', 'Status', 'FullName'], directions: ['asc', 'asc', 'asc'] },
                columnDefs: [{ field: 'ContactId', displayName: 'Contact Id', visible: false },
                            { field: 'OrganizationId', displayName: 'OrganizationId', visible: false },
                            { field: 'Organization', displayName: 'Organization', visible: false },
							{ field: 'FullName', displayName: 'Nombre de Contactos', width:300 },
                            { field: 'Modified', displayName: 'Actualización', width: 300 },
                            { field: 'Status', displayName: 'Estatus', cellTemplate: '<div class="ngCellText col9 colt9" ng-class="{red: (row.entity.StatusId != 0), green: (row.entity.StatusId == 0)}"><span ng-cell-text>{{row.getProperty(col.field)}}</span></div>', width: 200, headerClass: 'hidden' },
                            {
                            	displayName: '',
                            	cellTemplate: '<button id="deleteBtn" type="button" class="" ng-click="remove(row)" ng-show="row.entity.IsEditable" title="Eliminar">C</button>' +
									'<button id="detailBtn" type="button" class="" ng-click="detail(row)" title="Detalle">D</button>',
                                width: 85,
                                headerClass: 'hidden',
                                sortable: false
                            }],
                groups: ['Organization']
            };

            function getSelectedContacts() {
            	var rows = $scope.gridOptions.ngGrid.rowFactory.parsedData.length,
						selected = [],
						count = 0;

            	for (var i = rows; i--;) {
            		var row = $scope.gridOptions.ngGrid.rowFactory.parsedData[i];
            		if (!row.isAggRow && row.entity.Selected && row.entity.IsSelectable) {
            			selected[count++] = { ContactId: row.entity.ContactId };
            		}
            	}

            	return selected;
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