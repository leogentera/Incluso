angular
    .module('certificationtracker.contact.index', ['ngGrid'])
    .controller('contactListController', [
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

            $scope.createContact = function () {
            	_spinner.loading = true;
            	$location.path("/Contact/Create/");
            };

            $scope.detail = function (row) {
            	$location.path("/Contact/Detail/" + row.entity.ContactId);
            };

            $scope.remove = function (row) {
            	var modalOptions = {
            		closeButtonText: 'Cancelar',
            		actionButtonText: 'Eliminar',
            		headerText: 'Eliminar Contacto',
            		bodyText: '¿Desea eliminar al contacto?'
            	};

            	modalService.showModal({}, modalOptions).then(function () {
            		_spinner.loading = true;

            		contactservice
						.deleteContact(row.entity.ContactId)
						.success(function (data, status, headers, config) {
							$scope.gridOptions.ngGrid.rowFactory.selectionProvider.setSelection(row, false);
							row.entity.IsEditable = false;
							row.entity.IsSelectable = false;
							row.entity.StatusId = -1;
							row.entity.Status = 'Inactivo';
							_spinner.loading = false;
						})
						.error(function (data, status, headers, config) {
							_spinner.loading = false;
						    showModal('Error', data, 'Cerrar');
						});
            	});
            };

            $scope.sync = function () {
            	var selectedContacts = getSelectedContacts();
            	
                _spinner.loading = true;

                contactservice
				    .syncContacts(selectedContacts)
				    .success(function (data) {
				    	_spinner.loading = false;
					    if (selectedContacts.length == 1) {
					    	showModal('Guardar', 'El contacto seleccionado se ha guardado y se sincronizará con tus contactos de Exchange.', 'Cerrar');
					    }
					    else {
					    	showModal('Guardar', 'Los ' + selectedContacts.length + ' contactos seleccionados se han guardado y se sincronizarán con tus contactos de Exchange.', 'Cerrar');
					    }
					    				    })
				    .error(function (data, status, headers, config) {
					    _spinner.loading = false;
					    showModal('Error', data, 'Cerrar');
				    });
            };

            $scope.moveContacts = function () {
            	if ($scope.organization) {
            		var selectedContacts = getSelectedContacts();

            		if (selectedContacts.length) {
            			var modalOptions = {
            				closeButtonText: 'Cancelar',
            				actionButtonText: 'Mover',
            				headerText: 'Mover Contactos',
            				bodyText: '¿Desea mover los contactos? La lista de contactos se recargará.'
            			};

            			modalService.showModal({}, modalOptions).then(function () {
            				_spinner.loading = true;
            				var move = {
            					contacts: selectedContacts,
            					organizationId: $scope.organization
            				};

            				contactservice
								.moveContacts(move)
								.success(function (data) {
									showModal('Movimiento', 'Se movieron los contactos de organización.', 'Cerrar');
									$scope.getDataAsync();
								})
								.error(function (data, status, headers, config) {
									_spinner.loading = false;
								    showModal('Error', data, 'Cerrar');
								});
            			});
            		}
            		else {
            			showModal('Error', 'No ha seleccionado contactos.', 'Cerrar');
            		}
            	}
            	else
            	{
            		showModal('Error', 'Debe seleccionar una organización.', 'Cerrar');
            	}
            };

            $scope.requestUpdate = function () {
            	var selectedContacts = getSelectedContacts();

            	if (selectedContacts.length) {
            		$scope.message = '';

            		modalService.showModalCustom({
            			templateUrl: 'modalRequest',
            			backdrop: 'static',
            			windowClass: 'modal',
            			controller: ['$scope', '$modalInstance', 'contactservice', 'message',
							function ($scope, $modalInstance, contactservice, message) {
            				$scope.message = message;

            				$scope.ok = function (msg) {
            					_spinner.loading = true;
            					var update = {
            						message: msg,
            						contacts: selectedContacts
            					};

            					contactservice
									.requestUpdateContacts(update)
									.success(function (data) {
										_spinner.loading = false;
										showModal('Solicitud de actualización', 'Se ha enviado la solicitud a los contactos seleccionados.', 'Cerrar');
									})
									.error(function (data, status, headers, config) {
										_spinner.loading = false;
									    showModal('Error', data, 'Cerrar');
									});

            					$modalInstance.dismiss('cancel');
            				}

            				$scope.close = function () {
            					$modalInstance.dismiss('cancel');
            				};
            			}],
            			resolve: {
            				message: function () {
            					return $scope.message;
            				}
            			}
            		});
            	}
            	else {
            		showModal('Error', 'No ha seleccionado contactos.', 'Cerrar');
            	}
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

						    $scope.ok = function() {
						        var selectedContacts = getSelectedContactsToExport();

						        if (selectedContacts.length) {
						            _spinner.loading = true;
						            var toExport = {
						                contacts: selectedContacts,
						                organizationId: -1
						            };

						            contactservice
						                .exportContacts(toExport)
						                .success(function(data, status, headers, config) {
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
						                    setTimeout(function() {
						                        $('#hidden-excel-form').submit();
						                        // After get the file we delete it
						                        setTimeout(function() {
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
						                .error(function(data, status, headers, config) {
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
                        url: "/api/contact/getall",
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