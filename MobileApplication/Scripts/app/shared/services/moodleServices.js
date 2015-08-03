(function () {
    namespace('moodleFactory');

    moodleFactory.Services = (function(){

        var httpFactory = null;

        var _getAsyncProfile = function(userId, successCallback, errorCallback){
            _getAsyncData("profile", API_RESOURCE.format('user/' + userId), successCallback, errorCallback);
        };

        var _putAsyncProfile = function(userId, data, successCallback, errorCallback){
            _putAsyncData("profile", data, API_RESOURCE.format('user/' + userId), successCallback, errorCallback);
        };

        var _getAsyncUserCourse = function(userId, successCallback, errorCallback){
            _getAsyncData("usercourse", API_RESOURCE.format('usercourse/' + userId), successCallback, errorCallback);
        };

        var _getCacheObject = function(key){
            return localStorage.getItem(key);
        };

        var _getAsyncData = function(key, url, successCallback, errorCallback){
            httpFactory({
                method: 'GET',
                url: url, 
                headers: {'Content-Type': 'application/json'},
                }).success(function(data, status, headers, config) {
                    localStorage.setItem(key, JSON.stringify(data));
                    successCallback();
                }).error(function(data, status, headers, config) {
                    errorCallback(data);
            });
        };

        var _postAsyncData = function(key, data, url, successCallback, errorCallback){
            httpFactory({
                method: 'POST',
                url: url,
                data: data,
                headers: {'Content-Type': 'application/json'},
                }).success(function(data, status, headers, config) {
                    localStorage.setItem(key, JSON.stringify(data));
                    successCallback();
                }).error(function(data, status, headers, config) {
                    localStorage.setItem(key, JSON.stringify(data));
                    errorCallback();
            });
        };

        var _putAsyncData = function(key, dataModel, url, successCallback, errorCallback){
            httpFactory({
                method: 'PUT',
                url: url,
                data: dataModel,
                headers: {'Content-Type': 'application/json'},
                }).success(function(data, status, headers, config) {
                    localStorage.setItem(key, JSON.stringify(dataModel));
                    successCallback();
                }).error(function(data, status, headers, config) {
                    localStorage.setItem(key, JSON.stringify(dataModel));
                    errorCallback();
            });
        };

        var _setHttpFactory = function(http){
            httpFactory = http;
        };

        return {
            GetAsyncProfile: _getAsyncProfile,
            PutAsyncProfile: _putAsyncProfile,
            GetAsyncUserCourse: _getAsyncUserCourse,
            SetHttpFactory: _setHttpFactory,
            GetCacheObject: _getCacheObject
        };
    })();
}).call(this);
