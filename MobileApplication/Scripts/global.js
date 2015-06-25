//global variables

var API_RESOURCE = "http://api.gentera.com:8080/RestfulAPI/public/{0}";

var _courseId = 4;

var _IsOffline = function() {
	return false;
}

var _syncAll = function(blah) {

    var courses = new models.Courses();
    
    courses.stoage.clear();

    courses.storage.sync.pull({
    	success: blah
    });
}

