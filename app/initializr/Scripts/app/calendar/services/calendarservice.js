angular
   .module('certificationtracker.service.calendar', [])
   .factory('calendarservice', [
       '$http',
       function ($http) {
           return {
               createCalendar: function (calendar) {
                   return $http({
                       method: 'POST',
                       url: '/api/calendar/post',
                       data: calendar
                   });
               },

               readCalendar: function (calendarId) {
                   return $http({
                       method: 'GET',
                       url: '/api/calendar/get/' + calendarId
                   });
               },

               updateCalendar: function (calendar) {
                   return $http({
                       method: 'PUT',
                       url: '/api/calendar/put',
                       data: calendar
                   });
               },

               getDomainUsers: function (name) {
                   return $http({
                       method: 'GET',
                       url: '/api/calendar/getdomainusers/' + name
                   });
               },

               getPersonByName: function (name) {
                   return $http({
                       method: 'GET',
                       url: '/api/calendar/getpersonbyname/' + name
                   });
               },

               deleteExam: function (calendarId) {
                   return $http({
                       method: 'DELETE',
                       url: '/api/calendar/delete/' + calendarId
                   });
               },

               getPersonById: function (id) {
                   return $http({
                       method: 'GET',
                       url: '/api/calendar/getpersonbyid/' + id
                   });
               }
           };
       }]);