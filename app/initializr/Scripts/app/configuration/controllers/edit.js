angular
    .module('certificationtracker.configuration.edit', ['ui.select2'])
    .controller('configurationController', [
        '$scope',
        'configurationservice',
        'modalService',
        function ($scope, configurationservice, modalService) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope(),
				_timeEnum = {
					months: 1,
					years: 2
				};
        	_spinner.loading = true;

        	$scope.submitForm = function () {
        		$scope.configurationForm.submitted = true;
        	};

        	$scope.save = function () {
        		if ($scope.configurationForm.$valid) {
        			_spinner.loading = true;
        			var configuration = {
        				timeId: $scope.selectedTime,
        				time: $scope.configurationMonths,
        				size: $scope.configurationSize
        			};

        			configurationservice
						.saveConfiguration(configuration)
						.success(function (data, status, headers, config) {
							_spinner.loading = false;
							showModal('Configuración', 'Se ha guardado la configuración.', 'Cerrar');
						})
						.error(function (data, status, headers, config) {
							_spinner.loading = false;
							showModal('Error', data, 'Cerrar');
						});
        		}
            };

            configurationservice
				.readConfiguration()
				.success(function (data, status, headers, config) {
					if (data) {
						if (data.Configuration.Time % 12 == 0) {
							$scope.configurationMonths = data.Configuration.Time / 12;
							$scope.configurationSize = data.Configuration.Size;
							$scope.selectedTime = _timeEnum.years;
						} else {
							$scope.configurationMonths = data.Configuration.Time;
							$scope.configurationSize = data.Configuration.Size;
							$scope.selectedTime = _timeEnum.months;
						}
					}

					$scope.timeValues = [
						{ value: 1, time: 'Meses' },
						{ value: 2, time: 'Años' }
					];

					_spinner.loading = false;
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
        }]);