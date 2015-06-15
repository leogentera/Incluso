angular
    .module('certificationtracker.service.organization', [])
    .factory('organizationservice', [
        '$http',
        function ($http) {
            return {
            	createOrganization: function (organization) {
                    return $http({
                        method: 'POST',
                        url: '/api/organization/post',
                        data: organization,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

            	readOrganization: function (organizationId, isDetail) {
            		return $http({
            			method: 'GET',
            			url: '/api/organization/get/' + organizationId + '/' + isDetail,
            			headers: { 'RequestVerificationToken': $("#token").text() }
            		});
                },

            	updateOrganization: function (organization) {
                    return $http({
                        method: 'PUT',
                        url: '/api/organization/put',
                        data: organization,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

            	deleteOrganization: function (organizationId) {
                    return $http({
                        method: 'DELETE',
                        url: '/api/organization/delete/' + organizationId,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                }
            };
        }]);