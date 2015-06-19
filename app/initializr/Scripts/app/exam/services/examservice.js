angular
    .module('certificationtracker.service.exam', [])
    .factory('examservice', [
        '$http',
        function ($http) {
        	return {
        		createExam: function (exam) {
        			return $http({
        				method: 'POST',
        				url: '/api/exam/post',
        				data: exam
        			});
        		},

        		readExam: function (examId) {
        			return $http({
        				method: 'GET',
        				url: '/api/exam/get/' + examId
        			});
        		},

        		updateExam: function (exam) {
        			return $http({
        				method: 'PUT',
        				url: '/api/exam/put',
        				data: exam
        			});
        		},

        		deleteExam: function (examId) {
        			return $http({
        				method: 'DELETE',
        				url: '/api/exam/delete/' + examId
        			});
        		}
            };
        }]);