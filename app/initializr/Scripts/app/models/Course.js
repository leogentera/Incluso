﻿(function () {

    namespace('models');

    // --------------- Collection Model  ---------------

    models.Course = Backbone.Model.extend({

        urlRoot: API_RESOURCE.format('Course'),
});



}).call(this);