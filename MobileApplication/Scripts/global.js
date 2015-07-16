//global variables

var API_RESOURCE = "http://localhost:8080/RestfulAPI/public/{0}";

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

		var profile = new models.Profile();

		var hola = "";

	}
}

var _setToken = function(token) {
	$.ajaxSetup({
    	headers: { 'Access_token' : token.token }
	});
}


var dummyProfile = {
                    id: 2,
                    firstname: "Fernando",
                    fullname: "Fernando Gutierrez",
                    username: "fernando.gutierrez",
                    lastname: "Gtz",
                    country: "mx",
                    email: ["bernardo.garza@definityfirst.com"],
                    studies: [{
                        school: "Secundaria 50",
                        levelOfStudies: "Secundaria"
                    }],
                    address: {
                        street: "Alamo",
                        num_ext: "2922",
                        num_int: "",
                        colony: "Bosques del contry",
                        city: "Monterrey",
                        town: "Monterrey",
                        state: "Nuevo Leon",
                        country: "mx",
                        postalCode: "67176"
                    },
                    phones: [
                        "02322",
                        "3433"
                    ],
                    socialNetworks: [{
                        socialNetwork: "Facebook",
                        socialNetworkId: "casgar49"
                    }],
                    familiaCompartamos: [{
                        idClient: "4113",
                        relativeName: "Papa de Humberto",
                        relationship: "Padre"
                    }],
                    rank: 2,
                    stars: 56,
                    attributesAndQualities: ["Divertido", "Original"],
                    strengths: [],
                    recomendedBachelorDegrees: [],
                    likesAndPreferences: ["Face", "Dibujar"],
                    dreamsToBe: ["Ser Rico", "Poderoso"],
                    dreamsToHave: ["Esto", "Aquello"],
                    dreamsToDo: ["Esto", "Aquello"],
                    badgesEarned: [],
                    showMyInformation: true,
                    showAttributesAndQualities: false,
                    showLikesAndPreferences: false,
                    showBadgesEarned: false,
                    showStrengths: false,
                    showRecomendedBachelorDegrees: false,
                    showMyDreams: false
                };
localStorage.setItem("profile", JSON.stringify(dummyProfile));
