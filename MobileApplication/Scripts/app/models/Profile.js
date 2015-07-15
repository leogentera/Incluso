(function () {

    namespace('models');

    // --------------- Model  ---------------

    models.Profile = Backbone.Model.extend({

        //urlRoot: API_RESOURCE.format('Referral/single'),

        fetch: function(){
            var model = this;
            model.set(
                {
                    id: 2,
                    firstname: "Fernando",
                    fullname: "Gutierrez",
                    username: "fernando.gutierrez",
                    lastname: "Gtz",
                    country: "mx",
                    email: "bernardo.garza@definityfirst.com",
                    studies: [{
                        school: "Secundaria 50",
                        levelOfStudies: "terminada"
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
                    stage: "",
                    stars: 0,
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
                }
            );
            model.trigger('sync', model, null, options);
        }
    });

}).call(this);

