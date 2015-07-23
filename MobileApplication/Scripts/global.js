﻿//global variables

var API_RESOURCE = "http://localhost:8080/RestfulAPI/public/{0}";
//var API_RESOURCE = "http://localhost:8080/RestfulAPI/public/{0}";

var _courseId = 4;

var _IsOffline = function() {
	return false;
}

var _syncAll = function(callback) {


	console.log('is offline:' + _IsOffline());

	//check if the session is OnLine
	if (!_IsOffline()) {

		console.log('synching courses');

	    var courses = new models.Courses();
	    courses.storage.clear();
	    courses.storage.sync.pull({
	    	success: callback
	    });

		console.log('courses synced');

        syncProfile();
		callback();

	}
}

var _setToken = function(token) {
	$.ajaxSetup({
    	headers: { 'Access_token' : token.token }
	});
}

function syncProfile (){

    var dummyProfile = {
        "country": null,
        "email": "humberto_c49@hotmail.com",
        "firstname": "Admin",
        "fullname": "Admin User",
        "id": 2,
        "lastname": "User",
        "profileimageurl": "http:\/\/localhost\/moodle\/pluginfile.php\/5\/user\/icon\/f1",
        "profileimageurlsmall": "http:\/\/localhost\/moodle\/pluginfile.php\/5\/user\/icon\/f2",
        "username": "admin",
        "studies": [{
            "school": "Sec. 50",
            "levelOfStudies": "Secundaria"
        }],
        "address": {
            "street": "Alamo",
            "num_ext": "2922",
            "num_int": "",
            "colony": "Bosques del contry",
            "city": "Monterrey",
            "town": "Monterrey",
            "state": "Nuevo Leon",
            "country": null,
            "postalCode": "67176"
        },
        "phones": ["12345",
        "654987",
        "4113"],
        "socialNetworks": [{
            "socialNetwork": "Twitter",
            "socialNetworkId": "casgar49"
        },
        {
            "socialNetwork": "Facebook",
            "socialNetworkId": "casgar49"
        }],
        "familiaCompartamos": [{
            "idClient": "4113",
            "relativeName": "padre",
            "relationship": "papa"
        }],
        "rank": 2,
        "stars": "20",
        "attributesAndQualities": ["Educado",
        "Atento",
        "Inteligente"],
        "strengths": [],
        "recomendedBachelorDegrees": [],
        "likesAndPreferences": ["Escuchar musica"],
        "badgesEarned": [],
        "badgesToEarn": [{
            "id": "1",
            "name": "Testing Badge",
            "description": "A badge for testing!",
            "earned_times": "1",
            "points": "100",
            "dateIssued": null
        },
        {
            "id": "2",
            "name": "Second Badge",
            "description": "Adventure time Badge",
            "earned_times": "1",
            "points": "101",
            "dateIssued": null
        },
        {
            "id": "3",
            "name": "Kemosion",
            "description": "meme badge",
            "earned_times": null,
            "points": "1000",
            "dateIssued": null
        }],
        "dreamsToBe": ["Ingeniero", "Desarrollador de Software"],
        "dreamsToHave": ["Carro", "Casa"],
        "dreamsToDo": ["Plantar un hijo", "Leer un arbol", "Tener un libro"],
        "showMyInformation": true,
        "showAttributesAndQualities": true,
        "showLikesAndPreferences": true,
        "showBadgesEarned": true,
        "showStrengths": true,
        "showRecomendedBachelorDegrees": true,
        "showMyDreams": false,
        "alias": "Administrador",
        "termsAndConditions": true,
        "informationUsage": false,
        "status": "Enabled"
    };

    localStorage.setItem("profile", JSON.stringify(dummyProfile));

}