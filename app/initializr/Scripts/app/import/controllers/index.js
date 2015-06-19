angular
    .module('certificationtracker.import.index', [])
    .controller('importController', [
        '$scope',
        '$http',
		'modalService',
        function ($scope, $http, modalService) {
        	var _spinner = angular.element(document.getElementById('spinner')).scope();
        	_spinner.loading = true;

        	$http({
        		method: "POST",
        		url: "/api/import/import",
        		headers: { 'RequestVerificationToken': $("#token").text() }
        	})
			.success(function (data) {
				_spinner.loading = false;
				showModal(data.Success ? 'Importar' : 'Error', data.Message, 'Cerrar');
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