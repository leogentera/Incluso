angular
    .module('ui.sortable').value('uiSortableConfig', {
    	sortable: {
    		connectWith: '.column',
    		update: 'itemsChanged'
    	}
    });

// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('certificationtracker.contact.edit', ['ui.select2', 'ngTagsInput'])
    .controller('contactEditController', [
        '$scope',
        '$location',
        'contactservice',
        '$routeParams',
        'modalService',
		'$timeout',
		'$rootScope',
        function ($scope, $location, contactservice, $routeParams, modalService, $timeout, $rootScope) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope(),
				_statusEnum = {
					active: 0,
					outdated: 5,
					inactive: 10
				},
				_duplicated = {
					none: 0,
					simple: 1,
					hard: 2
				};

        	_spinner.loading = true;
            $Spelling.DefaultDictionary = "Espanol";
            $Spelling.ServerModel = "asp.net";
            $Spelling.ShowThesaurus = false;
            $Spelling.UserInterfaceTranslation = "es";
            
            $scope.isNew = angular.isUndefined($routeParams.id);
            $scope.fullNameValue = $("[name='fullName']");
        	$scope.title = '';
        	$scope.suffix = '';
        	$scope.isCollapsedContactInfo = false;
        	$scope.isCollapsedWebInfo = true;
        	$scope.isCollapsedOrganizationInfo = true;
        	$scope.isCollapsedPersonalInfo = true;
        	$scope.isCollapsedPhones = true;
        	$scope.isCollapsedOfficeAddress = true;
        	$scope.isCollapsedHomeAddress = true;
        	$scope.isCollapsedOtherAddress = true;
        	$scope.isCollapsedNotes = true;
        	$scope.isCollapsedSync = true;
            $scope.formerror = false;

        	$scope.contact = {
        		ContactId: -1,
        		First: '',
        		Middle: '',
        		Last: ''
        	};

        	$scope.contactDetail = {
        		PrincipalAddress: 0,
        		PrincipalEmail: 0
        	};

        	$rootScope.accounts = {
        		availables: null,
        		toSync: []
        	};

            $scope.openBirthday = function () {
            	$timeout(function () {
            		$scope.birthdayOpened = true;
            	});
            };

            $scope.openAnniversary = function () {
            	$timeout(function () {
            		$scope.anniversaryOpened = true;
            	});
            };

            $scope.returnToList = function () {
                $location.path("/Contact/Index");
            };

            $scope.syncWithAccounts = function () {
            	if ($scope.contact.OrganizationId) {
            		if (!$rootScope.accounts.availables) {
            			_spinner.loading = true;
            			var organization = {
            				organizationId: $scope.contact.OrganizationId,
            				pagination: false
            			};

            			contactservice
							.getAccountsByOrganization(organization)
							.success(function (data, status, headers, config) {
								if (data.Rows.length) {
									$rootScope.accounts.availables = data.Rows;
									showModalSyncWithAccounts();
								} else {
									showModal('Usuarios', 'La organización no tiene usuarios', 'Cerrar');
								}
								_spinner.loading = false;
							})
							.error(function (data, status, headers, config) {
								_spinner.loading = false;
								showModal('Error', data, 'Cerrar');
							});
            		} else {
            			showModalSyncWithAccounts();
            		}
            	}
            	else {
            		showModal('Error', 'No ha seleccionado organización.', 'Cerrar');
            	}
            };
            
            $scope.save = function () {
            	var first = $("[name='firstName']").val();
            	var middle = $("[name='middleName']").val();
            	var last = $("[name='lastName']").val();
            	var jobTitle = $("[name='jobTitle']").val();
            	var department = $("[name='department']").val();
            	var office = $("[name='office']").val();
            	var profession = $("[name='profession']").val();
            	var managersName = $("[name='managersName']").val();
            	var assistantsName = $("[name='assistantsName']").val();
            	var partner = $("[name='partner']").val();
            	var notes = $("[name='notes']").val();
            	$scope.contactForm.firstName.$setViewValue(first);
            	$scope.contactForm.middleName.$setViewValue(middle);
            	$scope.contactForm.lastName.$setViewValue(last);
            	$scope.contactForm.jobTitle.$setViewValue(jobTitle);
            	$scope.contactForm.department.$setViewValue(department);
            	$scope.contactForm.office.$setViewValue(office);
            	$scope.contactForm.profession.$setViewValue(profession);
            	$scope.contactForm.managersName.$setViewValue(managersName);
            	$scope.contactForm.assistantsName.$setViewValue(assistantsName);
            	$scope.contactForm.partner.$setViewValue(partner);
            	$scope.contactForm.notes.$setViewValue(notes);
            	validateDates();

            	if (isValidForm()) {
                	_spinner.loading = true;
            		var contactInfo = {
            			contact: $scope.contact,
            			contactDetail: $scope.contactDetail,
            			accountsToSync: $rootScope.accounts.toSync,
            			updateIfExists: false
            		};
            	    
            		contactInfo.contact.First = first;
            		contactInfo.contact.Middle = middle;
            		contactInfo.contact.Last = last;
            		contactInfo.contact.FullName = $scope.fullNameValue.val();
            		contactInfo.contactDetail.JobTitle = jobTitle;
            		contactInfo.contactDetail.Department = department;
            		contactInfo.contactDetail.Office = office;
            		contactInfo.contactDetail.Profession = profession;
            	    contactInfo.contactDetail.ManagersName = managersName;
            	    contactInfo.contactDetail.AssistantsName = assistantsName;
            	    contactInfo.contactDetail.SpousePartner = partner;
            	    contactInfo.contactDetail.Notes = notes;
            	    contactInfo.contactDetail.PrincipalEmail = validatePrincipalEmail();

            	    contactservice
						.validateIfExists(contactInfo)
						.success(function (data, status, headers, config) {
							if (data.Type == _duplicated.none) {
								$rootScope.confirmSave(contactInfo);
							} else if (data.Type == _duplicated.simple) {
								_spinner.loading = false;
								showModalUpdateContactIfExists(contactInfo, data.Total);
							}
						})
						.error(function (data, status, headers, config) {
							_spinner.loading = false;
							showModal('Error', data, 'Cerrar');
						});
            	} else {
                    $scope.contactForm.submitted = true;
                    $scope.formerror = true;
            	}
            };

            $rootScope.confirmSave = function (contactInfo) {
            	_spinner.loading = true;

            	if ($scope.isNew) {
            		contactservice
						.createContact(contactInfo)
						.success(function (data, status, headers, config) {
							$location.path("/Contact/Detail/" + headers('Location').substring(headers('Location').lastIndexOf('=') + 1));
						})
						.error(function (data, status, headers, config) {
							_spinner.loading = false;
							showModal('Error', data, 'Cerrar');
						});
            	} else {
            		contactservice
						.updateContact(contactInfo)
						.success(function (data, status, headers, config) {
							$location.path("/Contact/Detail/" + headers('Location').substring(headers('Location').lastIndexOf('=') + 1));
						})
						.error(function (data, status, headers, config) {
							_spinner.loading = false;
							showModal('Error', data, 'Cerrar');
						});
            	}
            };
            
            contactservice
                .readContact($routeParams.id || $scope.contact.ContactId)
                .success(function (data, status, headers, config) {
                	if (data.IsVisibleToUser) {
                		var isEditable = true;

                		if (data.Contact) {
                			if (data.IsEditable && data.Contact.StatusId != _statusEnum.inactive) {
                				$scope.contact = data.Contact;
                				$scope.contactDetail = data.ContactDetail;
                			} else {
                				showModal('Error', 'El contacto no puede ser editado.', 'Cerrar');
                				$location.path("/Contact/Detail/" + $routeParams.id);
                				isEditable = false;
                			}
                		} else if ($routeParams.id) {
                			showModal('Error', 'El contacto no es válido.', 'Cerrar');
                			$location.path("/Contact/Index");
                			isEditable = false;
                		}

                		if (isEditable) {
                			$scope.titles = data.Titles;
                			$scope.categories = data.Categories;
                			$scope.suffixes = data.Suffixes;
                			$scope.countries = data.Countries;

                            // This dummy field is necessary because in IE11 the first name is not loaded (it works when you press F5)
                			$Spelling.SpellCheckAsYouType('dummyName');
                			$Spelling.SpellCheckAsYouType('middleName');
                			$Spelling.SpellCheckAsYouType('firstName');
                			$Spelling.SpellCheckAsYouType('lastName');
                			$Spelling.SpellCheckAsYouType('jobTitle');
                			$Spelling.SpellCheckAsYouType('department');
                			$Spelling.SpellCheckAsYouType('office');
                			$Spelling.SpellCheckAsYouType('profession');
                			$Spelling.SpellCheckAsYouType('managersName');
                			$Spelling.SpellCheckAsYouType('assistantsName');
                			$Spelling.SpellCheckAsYouType('partner');
                			$Spelling.SpellCheckAsYouType('notes');
                		    
                			$("[name='dummyName']").val("Prueba");
                			$("[name='middleName']").val($scope.contact.Middle);
                			$("[name='firstName']").val($scope.contact.First);
                			$("[name='lastName']").val($scope.contact.Last);
                			$("[name='jobTitle']").val($scope.contactDetail.JobTitle);
                			$("[name='department']").val($scope.contactDetail.Department);
                			$("[name='office']").val($scope.contactDetail.Office);
                			$("[name='profession']").val($scope.contactDetail.Profession);
                			$("[name='managersName']").val($scope.contactDetail.ManagersName);
                			$("[name='assistantsName']").val($scope.contactDetail.AssistantsName);
                			$("[name='partner']").val($scope.contactDetail.SpousePartner);
                			$("[name='notes']").val($scope.contactDetail.Notes);

                			// To update the full name
                			$("#firstName___livespell_proxy").on("blur", function () {
                				var first = $("[name='firstName']").val();
                				$scope.contact.First = first;
                				setFullName();
                			});
                		    // Chrome
                			$("#jsspellcheck__element__0___livespell_proxy").on("blur", function () {
                				var middle = $("[name='middleName']").val();
                				$scope.contact.Middle = middle;
                				setFullName();
                			});
                			$("#jsspellcheck__element__1___livespell_proxy").on("blur", function () {
                				var last = $("[name='lastName']").val();
                				$scope.contact.Last = last;
                				setFullName();
                			});
                		    // IE && FF
                			$("#jsspellcheck__element__10___livespell_proxy").on("blur", function () {
                			    var middle = $("[name='middleName']").val();
                			    $scope.contact.Middle = middle;
                			    setFullName();
                			});
                			$("#jsspellcheck__element__11___livespell_proxy").on("blur", function () {
                			    var last = $("[name='lastName']").val();
                			    $scope.contact.Last = last;
                			    setFullName();
                			});

                			setTimeout(function () {
                				_spinner.loading = false;
                			}, 500);
                		}
                	} else {
                		showModal('Error', 'No tiene permisos para editar el contacto.', 'Cerrar');
                		$location.path("/Contact/Index");
                	}
                })
                .error(function (data, status, headers, config) {
                	_spinner.loading = false;
                    showModal('Error', data, 'Cerrar');
                });
            
            $scope.$watch('contact.TitleId', function (newVal, oldVal) {
            	if (newVal !== oldVal) {
            		$scope.title = getTitleById(newVal) + ' ';
            		setFullName();
            	}
            });

            $scope.$watch('contact.SuffixId', function (newVal, oldVal) {
            	if (newVal !== oldVal) {
            		$scope.suffix = getSuffixById(newVal) + ' ';
            		setFullName();
            	}
            });

            $scope.$watch('contact.OrganizationId', function (newVal, oldVal) {
            	if (newVal !== oldVal) {
            		$rootScope.accounts = {
            			availables: null,
            			toSync: []
            		};
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

            function showModalSyncWithAccounts() {
            	modalService.showModalCustom({
            		templateUrl: 'syncWithAccounts',
            		backdrop: 'static',
            		windowClass: 'modal',
            		controller: ['$rootScope', '$scope', '$modalInstance', 'contactservice', 'accounts',
						function ($rootScope, $scope, $modalInstance, contactservice, accounts) {
            			$scope.accounts = accounts;

            			$scope.ok = function () {
            				$rootScope.accounts = $scope.accounts;
            				$modalInstance.dismiss('cancel');
            			};

            			$scope.close = function () {
            				$modalInstance.dismiss('cancel');
            			};

            			$scope.remove = function (account) {
            				var idx = findIndexOf($scope.accounts.toSync, account.AccountId);
            				$scope.accounts.availables.push(account);
            				$scope.accounts.toSync.splice(idx, 1);
            			};

            			function findIndexOf(inArr, name) {
            				var total = inArr.length;
            				for (i = total; i--;) {
            					if (inArr[i].AccountId == name) {
            						return i;
            					}
            				}
            				return -1;
            			};
            		}],
            		resolve: {
            			accounts: function () {
            				var temporalAccounts = {
            					availables: [],
            					toSync: []
            				};

            				temporalAccounts.availables = $.map($rootScope.accounts.availables, function (item) {
            					return { AccountId: item.AccountId, First: item.First, Last: item.Last };
            				});

            				temporalAccounts.toSync = $.map($rootScope.accounts.toSync, function (item) {
            					return { AccountId: item.AccountId, First: item.First, Last: item.Last };
            				});

            				return temporalAccounts;
            			}
            		}
            	});
            };

            function showModalUpdateContactIfExists(contactInformation, duplicates) {
            	modalService.showModalCustom({
            		templateUrl: 'updateContactIfExists',
            		backdrop: 'static',
            		windowClass: 'modal',
            		controller: ['$rootScope', '$scope', '$modalInstance', 'contactservice', 'modalService', 'contactInfo', 'duplicates',
						function ($rootScope, $scope, $modalInstance, contactservice, modalService, contactInfo, duplicates) {
            			$scope.contactInfo = contactInfo;
            			$scope.canUpdate = duplicates < 2;

            			$scope.update = function () {
            				$scope.contactInfo.updateIfExists = true;
            				$rootScope.confirmSave($scope.contactInfo);
            				$modalInstance.dismiss('cancel');
            			};

            			$scope.createNew = function () {
            				_spinner.loading = true;

            				contactservice
								.requestManualValidation($scope.contactInfo)
								.success(function (data, status, headers, config) {
									showMessage('Contacto', 'Se ha enviado un correo al Administrador de Contactos para que realice la validación manualmente.', 'Cerrar');
									$location.path("/Contact/Detail/" + headers('Location').substring(headers('Location').lastIndexOf('=') + 1));
								})
								.error(function (data, status, headers, config) {
									_spinner.loading = false;
									showMessage('Error', data, 'Cerrar');
								});

            				$modalInstance.dismiss('cancel');
            			};

            			$scope.close = function () {
            				$modalInstance.dismiss('cancel');
            			};

            			function showMessage(header, message, action) {
            				var modalOptions = {
            					showCloseButton: false,
            					actionButtonText: action,
            					headerText: header,
            					bodyText: message
            				};

            				modalService.showModal({}, modalOptions);
            			};
            		}],
            		resolve: {
            			contactInfo: function () {
            				return contactInformation;
            			},
            			duplicates: function () {
            				return duplicates;
            			}
            		}
            	});
            };

            function getTitleById(titleId) {
            	var total = $scope.titles.length,
					name = '';

            	for (var i = total; i--;) {
            		if ($scope.titles[i].TitleId == titleId) {
            			name = $scope.titles[i].Name;
            			break;
            		}
            	}

            	return name;
            };

            function getSuffixById(suffixId) {
            	var total = $scope.suffixes.length,
					name = '';

            	for (var i = total; i--;) {
            		if ($scope.suffixes[i].SuffixId == suffixId) {
            			name = $scope.suffixes[i].Name;
            			break;
            		}
            	}

            	return name;
            };

            function setFullName() {
            	var result = $scope.title;
				
            	if ($scope.contact.First) {
            	    result += $scope.contact.First + ' ';
            	}
            	if ($scope.contact.Middle) {
            	    result += $scope.contact.Middle + ' ';
            	}
            	if ($scope.contact.Last) {
            	    result += $scope.contact.Last + ' ';
            	}

            	result += $scope.suffix;
                $scope.fullNameValue.val(result);
            };

            function validatePrincipalEmail() {
            	var principal = 0;
            	if ($scope.contactDetail.Email && $scope.contactDetail.PrincipalEmail == 1) {
            		principal = 1;
            	} else if ($scope.contactDetail.Email2 && $scope.contactDetail.PrincipalEmail == 2) {
            		principal = 2;
            	} else if ($scope.contactDetail.Email3 && $scope.contactDetail.PrincipalEmail == 3) {
            		principal = 3;
            	}
            	return principal;
            };

            function validateDates() {
            	// In Safari for Windows even when dates are valid the $scope.contactForm.$error.date is equal to an object
            	// Then by false-y values it returns true. The reason is unknown because we can debug in that browser.
            	// So we set the error to false when dates are valid
            	if ($scope.contactForm.anniversary.$valid && $scope.contactForm.birthday.$valid) {
            		$scope.contactForm.$error.date = false;
            	} else {
            		$scope.contactForm.$error.date = true;
            	}
            };

            function isValidForm() {
            	// In Safari for Windows, dates were creating conflicts in validation, so we create a custom validation
            	// Instead of using the $scope.contactForm.$valid we check for errors in the form
            	for (var key in $scope.contactForm.$error) {
            		if ($scope.contactForm.$error[key]) {
            			return false;
            		}
            	}

            	return true;
            };
        }]);