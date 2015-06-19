angular
    .module('certificationtracker.contact.detail', [])
    .controller('contactDetailController', [
        '$scope',
        '$location',
        'contactservice',
        '$routeParams',
        'modalService',
        function ($scope, $location, contactservice, $routeParams, modalService) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope();
        	_spinner.loading = true;

        	$scope.isNew = angular.isUndefined($routeParams.id);
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

        	$scope.returnToList = function () {
        		$location.path("/Contact/Index");
        	};

        	$scope.edit = function () {
        		_spinner.loading = true;
        		$location.path("/Contact/Edit/" + $routeParams.id);
        	};

        	$scope.reactivate = function () {
        		_spinner.loading = true;

        		contactservice
					.reactivate($scope.contact.ContactId)
					.success(function (data, status, headers, config) {
						$scope.isEditable = true;
						$scope.isReactivable = false;
						showModal('Contactos', 'El contacto ha sido reactivado.', 'Cerrar');
						_spinner.loading = false;
					})
					.error(function (data, status, headers, config) {
						_spinner.loading = false;
						showModal('Error', data, 'Cerrar');
					});
        	};

        	contactservice
                .readContact($routeParams.id || $scope.contact.ContactId)
                .success(function (data, status, headers, config) {
                	if (data.IsVisibleToUser) {
                		if (data.Contact) {
                			$scope.titles = data.Titles;
                			$scope.categories = data.Categories;
                			$scope.suffixes = data.Suffixes;
                			$scope.countries = data.Countries;
                			$scope.isEditable = data.IsEditable;
                			$scope.isReactivable = data.IsReactivable;

                			$scope.contact = data.Contact;
                			$scope.contactDetail = data.ContactDetail;

                			$scope.category = getCategoryById($scope.contact.OrganizationId);
                			$scope.businessAddressCountry = getCountryNameById($scope.contactDetail.BusinessAddressesCountryId);
                			$scope.homeAddressCountry = getCountryNameById($scope.contactDetail.HomeAddressesCountryId);
                			$scope.otherAddressCountry = getCountryNameById($scope.contactDetail.OtherAddressesCountryId);
                			$scope.assistantsPhone = getFullPhone($scope.contactDetail.AssistantsPhoneCountryId,
								$scope.contactDetail.AssistantsPhoneArea,
								$scope.contactDetail.AssistantsPhoneLocal,
								$scope.contactDetail.AssistantsPhoneExtension);
                			$scope.businessPhoneNumber = getFullPhone($scope.contactDetail.BusinessPhoneNumbersCountryId,
								$scope.contactDetail.BusinessPhoneNumbersArea,
								$scope.contactDetail.BusinessPhoneNumbersLocal,
								$scope.contactDetail.BusinessPhoneNumbersExtension);
                			$scope.businessPhoneNumber2 = getFullPhone($scope.contactDetail.BusinessPhoneNumbers2CountryId,
								$scope.contactDetail.BusinessPhoneNumbers2Area,
								$scope.contactDetail.BusinessPhoneNumbers2Local,
								$scope.contactDetail.BusinessPhoneNumbers2Extension);
                			$scope.businessPhoneNumberFax = getFullPhone($scope.contactDetail.BusinessPhoneNumbersFaxCountryId,
								$scope.contactDetail.BusinessPhoneNumbersFaxArea,
								$scope.contactDetail.BusinessPhoneNumbersFaxLocal,
								$scope.contactDetail.BusinessPhoneNumbersFaxExtension);
                			$scope.homePhoneNumber = getFullPhone($scope.contactDetail.HomePhoneNumbersCountryId,
								$scope.contactDetail.HomePhoneNumbersArea,
								$scope.contactDetail.HomePhoneNumbersLocal,
								$scope.contactDetail.HomePhoneNumbersExtension);
                			$scope.homePhoneNumber2 = getFullPhone($scope.contactDetail.HomePhoneNumbers2CountryId,
								$scope.contactDetail.HomePhoneNumbers2Area,
								$scope.contactDetail.HomePhoneNumbers2Local,
								$scope.contactDetail.HomePhoneNumbers2Extension);
                			$scope.homePhoneNumberFax = getFullPhone($scope.contactDetail.HomePhoneNumbersFaxCountryId,
								$scope.contactDetail.HomePhoneNumbersFaxArea,
								$scope.contactDetail.HomePhoneNumbersFaxLocal,
								$scope.contactDetail.HomePhoneNumbersFaxExtension);
                			$scope.phoneNumberMobile = getFullPhone($scope.contactDetail.PhoneNumbersMobileCountryId,
								$scope.contactDetail.PhoneNumbersMobileArea,
								$scope.contactDetail.PhoneNumbersMobileLocal,
								$scope.contactDetail.PhoneNumbersMobileExtension);
                			$scope.otherPhoneNumber = getFullPhone($scope.contactDetail.OtherPhoneNumbersCountryId,
								$scope.contactDetail.OtherPhoneNumbersArea,
								$scope.contactDetail.OtherPhoneNumbersLocal,
								$scope.contactDetail.OtherPhoneNumbersExtension);
                			_spinner.loading = false;
                		} else {
                			showModal('Error', 'El contacto no es válido.', 'Cerrar');
                			$location.path("/Contact/Index");
                		}
                	} else {
                		showModal('Error', 'No tiene permisos para visualizar la información del contacto.', 'Cerrar');
                		$location.path("/Contact/Index");
                	}
                })
                .error(function (data, status, headers, config) {
                	_spinner.loading = false;
                    showModal('Error', data, 'Cerrar');
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

        	function getFullPhone(countryId, area, local, extension) {
        		var phone = '';

        		if (countryId) {
        			phone += '+' + getCountryExtensionById(countryId) + ' ';
        		}
        		if (area) {
        			phone += '(' + area + ') ';
        		}
        		if (local) {
        			phone += local + ' ';
        		}
        		if (extension) {
        			phone += 'x ' + extension;
        		}

        		return phone;
        	};

        	function getCountryExtensionById(countryId) {
        		var total = $scope.countries.length,
					extension = '';

        		if (countryId) {
        			for (var i = total; i--;) {
        				if ($scope.countries[i].CountryId == countryId) {
        					extension = $scope.countries[i].Extension;
        					break;
        				}
        			}
        		}

        		return extension;
        	};

        	function getCountryNameById(countryId) {
        		var total = $scope.countries.length,
					name = '';

        		if (countryId) {
        			for (var i = total; i--;) {
        				if ($scope.countries[i].CountryId == countryId) {
        					name = $scope.countries[i].Name;
        					break;
        				}
        			}
        		}

        		return name;
        	};

        	function getCategoryById(categoryId) {
        		var total = $scope.categories.length,
					name = '';

        		if (categoryId) {
        			for (var i = total; i--;) {
        				if ($scope.categories[i].OrganizationId == categoryId) {
        					name = $scope.categories[i].Name;
        					break;
        				}
        			}
        		}

        		return name;
        	};
        }]);