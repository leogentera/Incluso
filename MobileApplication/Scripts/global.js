//global variables

var API_RESOURCE = "http://incluso.sieenasoftware.com/RestfulAPI/public/{0}";

var _courseId = 4;

var _httpFactory = null;

var _IsOffline = function() {
  return false;
}

var _syncAll = function(callback) {
  _syncCallback = callback;

  console.log('is offline:' + _IsOffline());

  //check if the session is OnLine
  if (!_IsOffline()) {
    moodleFactory.Services.GetAsyncProfile(_getItem("userId"), allServicesCallback);
  }
};

var allServicesCallback = function(){
  console.log("allServicesCallback");
  _syncCallback();
};

var _setToken = function(token) {
  $.ajaxSetup({
      headers: { 'Access_token' : token.token }
  });
};

var _setId = function(userId) {
  localStorage.setItem("userId", userId);
};

var _getItem = function(key) {
  return localStorage.getItem(key);
};

function syncCacheData (){

    //localStorage.setItem("profile", JSON.stringify(dummyProfile));
    //localStorage.setItem("user", JSON.stringify(User));
    //localStorage.setItem("course", JSON.stringify(Course));
    //localStorage.setItem("usercourse", JSON.stringify(UserCourse));

}

syncCacheData();
var logout = function($http, $scope, $location){
    console.log("Logout function ");
    $scope.currentUser = JSON.parse(moodleFactory.Services.GetCacheObject("CurrentUser"));
    $http(
      {
        method: 'POST',
        url: API_RESOURCE.format("authentication"), 
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        data: $.param(
            { token: $scope.currentUser.token,
              userid: $scope.currentUser.userId,
              action: "logout"})
      }
      ).success(function(data, status, headers, config) {

        console.log('successfully logout');

        localStorage.removeItem("CurrentUser");
        $location.path('/');
        }
      );
    };

