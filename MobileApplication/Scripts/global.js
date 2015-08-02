//global variables

var API_RESOURCE = "http://incluso.sieenasoftware.com/RestfulAPI/public/{0}";

var _courseId = 4;

var _IsOffline = function() {
  return false;
}

var _syncAll = function($http, callback) {


  console.log('is offline:' + _IsOffline());

  //check if the session is OnLine
  if (!_IsOffline()) {

    //console.log('synching courses');

      //var courses = new models.Courses();
      //courses.storage.clear();
      //courses.storage.sync.pull({
      //  success: callback
      //});

    //console.log('courses synced');

    moodleFactory.Services.SetHttpFactory($http);
    moodleFactory.Services.GetAsyncProfile(_getItem("userId"), callback);
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
