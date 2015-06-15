angular
    .module('certificationtracker.service.report', [])
    .factory('reportservice', [
        '$http',
        function ($http) {
            return {
                readActivities: function () {
                    return $http({
                        method: 'GET',
                        url: '/api/report/getactivities',
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

                generateReport: function (report) {
                    return $http({
                        method: 'POST',
                        url: '/api/report/generateactivityreport',
                        data: report,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

                generateCreateOrUpdateReport: function (report) {
                    return $http({
                        method: 'POST',
                        url: 'api/report/generatecreateorupdatereport',
                        data: report,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

                informationReport: function (activitylogid) {
                    return $http({
                        method: 'GET',
                        url: 'api/report/getinformationreport/' + activitylogid,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                },

                informationModifiedReport: function (activitylogid) {
                    return $http({
                        method: 'GET',
                        url: 'api/report/getinformationmodifiedreport/' + activitylogid,
                        headers: { 'RequestVerificationToken': $("#token").text() }
                    });
                }
            };
        }]);