(function () {
    namespace('moodleFactory');

    moodleFactory.Services = (function(){

        var httpFactory = null;

        var _getAsyncProfile = function(userId, successCallback, errorCallback){
            _getAsyncData("profile", API_RESOURCE.format('user/' + userId), successCallback, errorCallback);
        };

        var _getCacheProfile = function(){
            return localStorage.getItem("profile");
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

        var _setHttpFactory = function(http){
            httpFactory = http;
        };

        return {
            GetAsyncProfile: _getAsyncProfile,
            SetHttpFactory: _setHttpFactory,
            GetCacheProfile: _getCacheProfile
        };
    })();
}).call(this);
