//global variables

var API_RESOURCE = "http://incluso.sieenasoftware.com/RestfulAPI/public/{0}";

var _courseId = 4;

var _IsOffline = function() {
  return false;
}

var _syncCalls = 0;
var _syncCallback = null;

var _syncAll = function($http, callback) {
  _syncCallback = callback;

  console.log('is offline:' + _IsOffline());

  //check if the session is OnLine
  if (!_IsOffline()) {
    moodleFactory.Services.SetHttpFactory($http);
    moodleFactory.Services.GetAsyncProfile(_getItem("userId"), allServicesCallback);
    moodleFactory.Services.GetAsyncUserCourse(_getItem("userId"), getAsyncUserCourseCallBack);
  }
};

var getAsyncUserCourseCallBack = function(){
  allServicesCallback();
  var userCourse = JSON.parse(moodleFactory.Services.GetCacheObject("usercourse"));
  moodleFactory.Services.GetAsyncCourse(userCourse.courseId, allServicesCallback);
};

var allServicesCallback = function(){
  var _totalSyncCalls = 3;

  _syncCalls = _syncCalls + 1;
  if(_syncCalls === _totalSyncCalls){
    console.log("allServicesCallback");
    _syncCallback();
  }
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
