(function () {

    namespace('views');

    // --------------- Collection View  ---------------
    views.DashboardView = Backbone.View.extend({

        el: '#main',

        model: null,

        initialize: function (options) {
            
            //this.listenTo(this.collection, 'sync', this.render, this);          
        },

        render: function () {
            this.$el.html("<p>this is the dashboard view</p>");
        },

        close: function () {
            this.off();
            this.undelegateEvents;
            return this.remove();
        }
    });
}).call(this);
