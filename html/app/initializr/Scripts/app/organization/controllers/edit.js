angular
    .module('ui.sortable').value('uiSortableConfig', {
    sortable: {
        connectWith: '.column',
        update: 'itemsChanged'
    }
});

angular
    .module('certificationtracker.organization.edit', ['ui.sortable'])
    .controller('organizationEditController', [
        '$scope',
        '$location',
        'organizationservice',
        '$routeParams',
		'modalService',
        function ($scope, $location, organizationservice, $routeParams, modalService) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope();

        	_spinner.loading = true;
        	$scope.isNew = angular.isUndefined($routeParams.id);

        	$scope.organization = {
        		AvailableAccounts: [],
        		Organization: {
        			SelectedAccounts: []
        		}
        	};

        	$scope.itemsChanged = function () {
        	    if ($scope.organization.Organization.SelectedAccounts.length == 0) {
        	        $("#recipient").css("background", "");
        	    }
        	    else {
        	        $("#recipient").css("background", "transparent");
        	    }
        	};
            
            $scope.returnToList = function () {
            	$location.path("/Organization/Index");
            };

            $scope.submitForm = function () {
                $scope.organizationForm.submitted = true;
            };

            $scope.remove = function (account) {
                var idx = findIndexOf($scope.organization.Organization.SelectedAccounts, account.AccountId);
                $scope.organization.AvailableAccounts.push(account);
                $scope.organization.Organization.SelectedAccounts.splice(idx, 1);
            };

            $scope.save = function() {
            	if ($scope.organizationForm.$valid) {
            		_spinner.loading = true;

            		if ($scope.isNew) {
            			organizationservice
							.createOrganization($scope.organization.Organization)
							.success(function (data, status, headers, config) {
								$location.path("/Organization/Detail/" + headers('Location').substring(headers('Location').lastIndexOf('=') + 1));
							})
							.error(function (data, status, headers, config) {
								_spinner.loading = false;
							    showModal('Error', data, 'Cerrar');
							});
            		} else {
            			organizationservice
							.updateOrganization($scope.organization.Organization)
							.success(function (data, status, headers, config) {
							    $location.path("/Organization/Detail/" + headers('Location').substring(headers('Location').lastIndexOf('=') + 1));
							})
							.error(function (data, status, headers, config) {
								_spinner.loading = false;
							    showModal('Error', data, 'Cerrar');
							});
            		}
            	}
            };

            organizationservice
                .readOrganization($routeParams.id || -1, false)
                .success(function (data, status, headers, config) {
                	if (data.Organization) {
                		$scope.organization.Organization = data.Organization;
                	}

                	$scope.organization.AvailableAccounts = data.AvailableAccounts;

                    if ($scope.organization.Organization.SelectedAccounts.length == 0) {
                        $("#recipient").css("background", "");
                    }
                    else {
                        $("#recipient").css("background", "transparent");
                    }
                    _spinner.loading = false;
                })
				.error(function (data, status, headers, config) {
					_spinner.loading = false;
					showModal('Error', data, 'Cerrar');
				});
            
            function findIndexOf(inArr, name) {
                var total = inArr.length;
                for (i = total; i--;) {
                    if (inArr[i].AccountId == name) {
                        return i;
                    }
                }
                return -1;
            }

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