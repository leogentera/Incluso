angular
    .module('certificationtracker.service.configuration', [])
    .factory('configurationservice', [
        '$http',
         function ($http) {
             return {
                 saveConfiguration: function (configuration) {
                     return $http({
                         method: 'POST',
                         url: 'api/configuration/post',
                         data: configuration,
                         headers: { 'RequestVerificationToken': $("#token").text() }
                     });
                 },

                 readConfiguration: function () {
                 	return $http({
                 		method: 'GET',
                 		url: '/api/configuration/getconfigurations',
                 		headers: { 'RequestVerificationToken': $("#token").text() }
                 	});
                 }
             }
         }]);