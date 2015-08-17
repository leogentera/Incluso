// http://weblogs.asp.net/dwahlin/archive/2013/09/18/building-an-angularjs-modal-service.aspx
angular
    .module('incluso.programa.profile', [])
    .controller('programaProfileController', [
        '$q',
        '$scope',
        '$location',
        '$routeParams',
        '$timeout',
        '$rootScope',
        '$http',
        function ($q, $scope, $location, $routeParams, $timeout, $rootScope, $http) {

            _httpFactory = $http;

            $scope.currentPage = 1;
            $scope.model = getDataAsync();
            $scope.genderItems = ['Masculino', 'Femenino'];
            $scope.countryItems = ['México', 'Guatemala', 'Costa Rica', 'Perú', 'Brasil'];
            $scope.cityItems = ['DF', 'Guadalajara', 'Monterrey', 'Villa hermosa'];
            $scope.stateItems = ['Coahuila', 'Jalisco', 'México', 'Nuevo León'];
            $scope.maritalStatusItems = ['Soltero(a)', 'Casado(a)', 'Unión libre'];
            $scope.studiesList = ['Primaria', 'Secundaria', 'Preparatoria', 'Universidad'];









            formatDate($scope.model.birthday);


            function getDataAsync() {

                var m = JSON.parse(moodleFactory.Services.GetCacheObject("profile"));


                if (!m) {
                    $location.path('/');
                    return "";
                }
                initFields(m);

                return m;
            }

            function formatDate(date) {
                var splitDate = date.split("/");
                var newDate = splitDate[2] + "-" + splitDate[1] + "-" + splitDate[0];
                //var newDate = new Date(splitDate[2], splitDate[1], splitDate[0]);
                
                getAge(newDate);

                $scope.model.birthday = newDate;
            }

            function getAge(date) {
                if (date != null || date != '') {
                    var splitDate = date.split("-");
                    var birthDate = new Date(splitDate[0], splitDate[1], splitDate[2]);
                    var cur = new Date();
                    var diff = cur - birthDate;
                    var age = Math.floor(diff / 31536000000);
                    $scope.model.age = age;
                }
            }

            function initFields(m) {
                if (m.address.street == null) { m.address.street = ""; }
                if (m.address.num_ext == null) { m.address.num_ext = ""; }
                if (m.address.num_int == null) { m.address.num_int = ""; }
                if (m.address.colony == null) { m.address.colony = ""; }
                if (m.address.city == null) { m.address.city = ""; }
                if (m.address.town == null) { m.address.town = ""; }
                if (m.address.state == null) { m.address.state = ""; }
                if (m.address.postalCode == null) { m.address.postalCode = ""; }
            }

            $scope.navigateToPage = function (pageNumber) {
                $scope.currentPage = pageNumber;
            };

            $scope.edit = function () {
                $location.path('/Perfil/Editar');
            }

            $scope.index = function () {
                $location.path('/Perfil');
            }

            $scope.navigateToDashboard = function () {
                $location.path('/ProgramaDashboard');
            }

            $scope.save = function () {
                moodleFactory.Services.PutAsyncProfile(_getItem("userId"), $scope.model,
                    function (data) {
                        alert("1: " + data)
                        $scope.index();
                    },
                    function (date) {
                        alert("1: " + date)
                        $scope.index();
                    });
            }

            $scope.addStudy = function () {
                $scope.model.studies.push({});
            }
            $scope.deleteStudy = function (index) {
                $scope.model.studies.splice(index, 1);
            }

            $scope.addPhone = function () {
                $scope.model.phones.push(new String());
            }
            $scope.deletePhone = function (index) {
                $scope.model.phones.splice(index, 1);
            }

            $scope.addFavoriteSports = function (index) {
                $scope.model.favoriteSports.push(new String());
            }

            $scope.deleteFavoriteSports = function (index) {
                $scope.model.favoriteSports.splice(index, 1);
            }

            $scope.addArtisticActivities = function (index) {
                $scope.model.artisticActivities.push(new String());
            }

            $scope.deleteArtisticActivities = function (index) {
                $scope.model.artisticActivities.splice(index, 1);
            }

            $scope.addTalents = function (index) {
                $scope.model.talents.push(new String());
            }

            $scope.deleteTalents = function (index) {
                $scope.model.talents.splice(index, 1);
            }

            $scope.addValue = function (index) {
                $scope.model.values.push(new String());
            }

            $scope.deleteValue = function (index) {
                $scope.model.values.splice(index, 1);
            }

            $scope.addHabilitie = function (index) {
                $scope.model.habilities.push(new String());
            }

            $scope.deleteHabilitie = function (index) {
                $scope.model.habilities.splice(index, 1);
            }

            $scope.addInspirationalCharacter = function (index) {
                $scope.model.inspirationalCharacters.push(new String());
            }

            $scope.deleteInspirationalCharacter = function (index) {
                $scope.model.inspirationalCharacters.splice(index, 1);
            }

            $scope.addMainActivity = function (index) {
                $scope.model.mainActivity.push(new String());
            }

            $scope.deleteMainActivity = function (index) {
                $scope.model.mainActivity.splice(index, 1);
            }

            $scope.addMoneyIncome = function (index) {
                $scope.model.moneyIncome.push(new String());
            }

            $scope.deleteMoneyIncome = function (index) {
                $scope.model.moneyIncome.splice(index, 1);
            }

            $scope.addKnownDevice = function (index) {
                $scope.model.knownDevices.push(new String());
            }

            $scope.deleteKnownDevice = function (index) {
                $scope.model.knownDevices.splice(index, 1);
            }

            $scope.addKnOwnDevice = function (index) {
                $scope.model.ownDevices.push(new String());
            }

            $scope.deleteOwnDevice = function (index) {
                $scope.model.ownDevices.splice(index, 1);
            }

            $scope.addPhoneUsage = function (index) {
                $scope.model.phoneUsage.push(new String());
            }

            $scope.deletePhoneUsage = function (index) {
                $scope.model.phoneUsage.splice(index, 1);
            }

            $scope.addKindOfVideoGame = function (index) {
                $scope.model.kindOfVideoGames.push(new String());
            }

            $scope.deleteKindOfVideoGame = function (index) {
                $scope.model.kindOfVideoGames.splice(index, 1);
            }

            $scope.addFavoriteGame = function (index) {
                $scope.model.favoriteGames.push(new String());
            }

            $scope.deleteFavoriteGame = function (index) {
                $scope.model.favoriteGames.splice(index, 1);
            }

            $scope.addEmail = function () {
                var existingEmail = $scope.model.email;
                if (existingEmail) {
                    $scope.model.additionalEmails.push(new String());
                }
            }

            $scope.logout = function () {
                logout($http, $scope, $location);
            };

            $scope.deleteAdditionalEmails = function (index) {
                $scope.model.additionalEmails.splice(index, 1);
            }

            $scope.addSocialNetwork = function () {
                $scope.model.socialNetworks.push({});
            }
            $scope.deleteSocialNetwork = function (index) {
                $scope.model.socialNetworks.splice(index, 1);
            }

            $scope.addFamiliaCompartamos = function () {
                $scope.model.familiaCompartamos.push({});
            }
            $scope.deleteFamiliaCompartamosk = function (index) {
                $scope.model.familiaCompartamos.splice(index, 1);
            }

            var $selects = $('select.form-control');
            $selects.change(function () {
                $elem = $(this);
                $elem.addClass('changed');
            });
        }]);
