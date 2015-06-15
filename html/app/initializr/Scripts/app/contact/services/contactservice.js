angular
    .module('certificationtracker.service.contact', [])
    .factory('contactservice', [
        '$http',
        function ($http) {
            return {
                createContact: function (contact) {
                    return $http({
                        method: 'POST',
                        url: '/api/contact/post',
                        data: contact,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

                readContact: function (contactId) {
                	return $http({
                		method: 'GET',
                		url: '/api/contact/get/' + contactId,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                updateContact: function (contact) {
                    return $http({
                        method: 'PUT',
                        url: '/api/contact/put',
                        data: contact,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

                deleteContact: function (contactId) {
                    return $http({
                        method: 'DELETE',
                        url: '/api/contact/delete/' + contactId,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

                syncContacts: function (contacts) {
                	return $http({
                		method: 'POST',
                		url: '/api/contact/sync',
                		data: contacts,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                moveContacts: function (move) {
                	return $http({
                		method: 'POST',
                		url: '/api/contact/move',
                		data: move,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                requestUpdateContacts: function (update) {
                	return $http({
                		method: 'POST',
                		url: '/api/contact/requestupdate',
                		data: update,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                getAccountsByOrganization: function (organization) {
                	return $http({
                		method: 'GET',
                		url: '/api/account/getallbyorganizationid',
                		params: organization,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                requestManualValidation: function (contactInfo) {
                	return $http({
                		method: 'POST',
                		url: '/api/contact/requestmanualvalidation',
                		data: contactInfo,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                validateIfExists: function (contact) {
                	return $http({
                		method: 'POST',
                		url: '/api/contact/validateifexists',
                		data: contact,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                reactivate: function (contactId) {
                	return $http({
                		method: 'POST',
                		url: '/api/contact/reactivate/' + contactId,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                exportContacts: function (toExport) {
                	return $http({
                		method: 'POST',
                		url: '/api/contact/export',
                		data: toExport,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                },

                syncContactsCeo: function (contacts) {
                	return $http({
                		method: 'POST',
                		url: '/api/contact/syncceo',
                		data: contacts,
                		headers: { 'RequestVerificationToken': $("#token").text() }
                	});
                }
            };
        }]);