(function () {

    namespace('views');

    // --------------- Collection View  ---------------
    views.ProfileView = Backbone.View.extend({

        el: '#main',

        initialize: function (options) {
            this.listenTo(this.model, 'sync', this.render);
        },

        render: function () {
            this.$el.html("<p>this is the profile view</p>");
        },

        close: function () {
            this.off();
            this.undelegateEvents;
            return this.remove();
        }
    });
}).call(this);
