//global variables

var API_RESOURCE = "http://incluso.sieenasoftware.com/RestfulAPI/public/{0}";

var _courseId = 4;

var _IsOffline = function() {
	return false;
}

var _syncAll = function(blah) {


	console.log('is offline:' + _IsOffline());

	//check if the session is OnLine
	if (!_IsOffline()) {

		console.log('synching courses');

	    var courses = new models.Courses();
	    courses.storage.clear();
	    courses.storage.sync.pull({
	    	success: blah
	    });

		console.log('courses synced');

	}
}

var _setToken = function(token) {
//	$.ajaxSetup({
//    	headers: { 'Access_token' : token.token }
//	});
}

