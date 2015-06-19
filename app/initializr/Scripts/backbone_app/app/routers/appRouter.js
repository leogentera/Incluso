(function () {

    namespace('routers');

    routers.AppRouter = new (Backbone.Router.extend({

        routes: {
            "": "dashboard",
            "profile": "profile"
        },

        initialize: function () {
            

            $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
                options.crossDomain = {
                    crossDomain: true
                };
            });
        },

        views: {
            dashboardView: null,
            profileView: null
        },

        start: function () {
            Backbone.history.start();
        },

        model: {
            currentUser: null,
            leaders: null
        },

        dashboard: function () {
            var courses = new models.Courses();

            if (!this.dashboardView) {
                this.dashboardView = new views.DashboardView({ collection: courses });
            } else {
                dashboardView.show();
            }

            //courses.fetch();
            if (!Offline.onLine()) {
                var c = courses.storage.sync.pull();
                var d = c.responseJSON;
                var x = 1;
                var y = 3;
                var z = x + y;
                alert(z);
            } else {
                courses.fetch({ 
                    local: true,
                    success: function (data) {
                        alert(data.models.length);
                    },
                    });
                var f = c.responseJSON;
            }
        },

        profile: function () {    
            alert('profile');
        }

       
    }));
}).call(this);

