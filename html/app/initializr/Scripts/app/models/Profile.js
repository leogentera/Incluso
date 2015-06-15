(function () {

    namespace('models');

    // --------------- Model  ---------------

    models.Profile = Backbone.Model.extend({

        //urlRoot: API_RESOURCE.format('Referral/single'),

        fetch: function(){
            var model = this;
            model.set(
                {
                    firstName: "Fernando",
                    lastName: "Gutierrez",
                    username: "fernando.gutierrez",
                    Age: "40"
                }
            );
            model.trigger('sync', model, null, options);
        }
    });

}).call(this);

