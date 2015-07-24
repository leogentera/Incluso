//global variables

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

        syncCacheData();
		callback();

	}
}

var _setToken = function(token) {
	$.ajaxSetup({
    	headers: { 'Access_token' : token.token }
	});
}

function syncCacheData (){

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

    var User = {
          ID: 2,
          Alias: "Leonardo",
          Username: "leogarza955",
          Fullname: "Leonardo Garza R",
          GlobalProgress: 20,
          Badge:[ 
            {
              ID: 3,
              name: "Musical",
              description: "Lorem ipsum",
              earned_times: 3,
              points: 20,
              dateIssued: 14052015
            },
            {
              ID: 4,
              name: "Musical",
              description: "Lorem ipsum",
              earned_times: 3,
              points: 20,
              dateIssued: 14052015
            }
          ],
          Shield: "img-badges-blocked.png",
          Stars: 240,
          Place: 28,
          Schools:[
            "Instituto de educación media superior",
            "Instituto de educación secundaria"
          ],
          Address:{
            Street: "Av Insurgentes Sur",
            ExteriorNumber: "1048",
            Suburb: "Actipan",
            City: "Benito Juárez",
            Town: "Benito Juárez",
            State: "Ciudad de México",
            PostalCode: "03230",
            Country: "México"
          },
          Phone: "55-1537-2672",
          Email: "leogarza955@gmail.com",
          SocialNetwork:[
            {
              Type: "Twitter",
              Value: "@leogarza955"
            },
            {
              Type: "Facebook",
              Value:"leogarza.955"
            }
          ],
          FamiliaCompartamos: [
            {
              Client: 8920093,
              Name: "Luis Enrique Garza Treviño",
              Relationship: "Padre"
            },
            {
              Client: 8920094,
              Name: "Maria Guadalupe Pedraza de Garza",
              Relationship: "Madre"
            }
          ],
          Dreams:[
            {
              ID: 2,
              Value: "Terminar mis estudios"
            },
            {
              ID: 3,
              Value: "Entrar a cursos de música"
            },
            {
              ID: 4,
              Value: "Ser más responsable con el medio ambiente"
            }
          ],
          Hobbies:[
            {
              ID: 2,
              Description: "Deportes",
              Value: 0
            },
            {
              ID: 3,
              Description: "Carpintería",
              Value: 0
            },
            {
              ID: 4,
              Description: "Pintura",
              Value: 1
            }   
          ],
          Qualities:[
            {
              ID: 2,
              Description: "Aprendo Rápido",
              Value: 1
            },
            {
              ID: 3,
              Description: "Me comunico claramente",
              Value: 1
            },
            {
              ID: 4,
              Description: "Emprendedor",
              Value: 0
            }
          ],
          Strengths:[
            {
              ID: 2,
              Type: "Musical",
              Description: "Lorem ipsum",
              Value: "Alto"
            },
            {
              ID: 3,
              Type: "Interpersonal",
              Description: "Lorem ipsum",
              Value: "Alto"
            },
            {
              ID: 4,
              Type: "Naturalista",
              Description: "Lorem ipsum",
              Value: "Alto"
            },
            {
              ID: 5,
              Type: "Intrapersonal",
              Description: "Lorem ipsum",
              Value: "Medio"
            }
          ]
        };


    var Course = {
          Leaderboard:[
            {
              UserID: 2,
              Fullname: "José Alberto Gónzalez",
              Progress: 80,
              Stars: 250,
            },
            {
              UserID: 3,
              Fullname: "Samantha García",
              Progress: 70,
              Stars: 240,
            },
            {
              UserID: 4,
              Fullname: "Araceli Martínez",
              Progress: 68,
              Stars: 235,
            }
          ],
          Stages: [
            {
              Id: 1,
              Name: "Inspirar",
              Description: "Inspirar es blah blah",
              Activities : [
              {
                Id : 12,
                Name: "Descubre mas",
                Description: "lorem ipsum bla",
                Image: "descubre.png",
                ActivityType: "ActivityManager",
                Status: 1,
                Activities: [
                  {
                    Id : 12,
                    Name: "Descubre mas",
                    Description: "lorem ipsum bla",
                    ActityType: "Forum"
                  },
                  {
                    Id : 12,
                    Name: "Descubre mas",
                    Description: "lorem ipsum bla",
                    ActityType: "Formulario"               
                  }
                ]
              }]
            }
          ]
        };

        var UserCourse = {
          CourseID: 2,
          UserID: 3,
          Stages: [
            {
              StageID: 1,
              Status: 0,
              Activities : [
                {
                  ActivityID : 12,
                  Status: 1,
                  Activities: [
                    {
                      ActivityID : 13,
                      Status: 1
                    },
                    {
                      ActivityID : 14,
                      Status: 1                     
                    }
                  ]
                },
                {
                  ActivityID : 15,
                  Status: 0,
                  Activities: [
                    {
                      ActivityID : 16,
                      Status: 1
                    },
                    {
                      ActivityID : 17,
                      Status: 0                     
                    }
                  ]
                }
              ]
            },
            {
              StageID: 2,
              Status: 1,
              Activities : [
                {
                  ActivityID : 18,
                  Status: 1,
                  Activities: [
                    {
                      ActivityID : 19,
                      Status: 1
                    },
                    {
                      ActivityID : 20,
                      Status: 1                     
                    }
                  ]
                },
                {
                  ActivityID : 21,
                  Status: 0,
                  Activities: [
                    {
                      ActivityID : 22,
                      Status: 1
                    },
                    {
                      ActivityID : 23,
                      Status: 0                     
                    }
                  ]
                }
              ]
            }
          ]
        };

    localStorage.setItem("profile", JSON.stringify(dummyProfile));
    localStorage.setItem("user", JSON.stringify(User));
    localStorage.setItem("course", JSON.stringify(Course));
    localStorage.setItem("usercourse", JSON.stringify(UserCourse));

}