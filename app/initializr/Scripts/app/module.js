angular
    .module('contactmaster', [
        'ngRoute',
        'ngSanitize',
        'ngResource',
        'ui.bootstrap',
        'ui.bootstrap.tpls',
        'certificationtracker.service.dialog',
        'certificationtracker.home',
        // One module per controller. If we wanted to use one module for several controllers we would need to load dependencies of
        // one controller for all controllers in the module, and we would also need a variable to keep track of the modules:
        // http://zinkpulse.com/organizing-modules-in-angularjs/ and http://cliffmeyers.com/blog/2013/4/21/code-organization-angularjs-javascript
        //'certificationtracker.contact.detail',
        //'certificationtracker.contact.edit',
        //'certificationtracker.contact.index',
        //'certificationtracker.service.contact',
		//'certificationtracker.account.detail',
        //'certificationtracker.account.edit',
        //'certificationtracker.account.index',
        //'certificationtracker.service.account',
		//'certificationtracker.organization.detail',
        //'certificationtracker.organization.edit',
        //'certificationtracker.organization.index',
        //'certificationtracker.service.organization',
        //'certificationtracker.shared.logout',
        //'certificationtracker.report.activityreport',
        //'certificationtracker.report.updatecontactreport',
        //'certificationtracker.service.report',
        //'certificationtracker.configuration.edit',
        //'certificationtracker.service.configuration',
		//'certificationtracker.import.index',
		//'certificationtracker.resendemail.index',
		//'certificationtracker.contact.ceo',
		'certificationtracker.calendar.index',
		'certificationtracker.calendar.global',
		'certificationtracker.calendar.weekly',
		'certificationtracker.calendar.edit',
		'certificationtracker.calendar.search',
		'certificationtracker.service.calendar'
		//'certificationtracker.exam.index',
		//'certificationtracker.exam.edit',
		//'certificationtracker.service.exam'
    ])
    .config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
        $routeProvider.when('/', {
        	templateUrl: '/Templates/Calendar/Index.html',
        	controller: 'calendarGlobalController'
        });
        //$routeProvider.when('/Home/Index', {
        //    templateUrl: '/Home/Content'
        //});
        //$routeProvider.when('/Home/_LoginPartial', {
        //    templateUrl: '/Home/Content'
        //});
        //$routeProvider.when('/Contact/Index', {
        //    templateUrl: '/Contacts/Index',
        //    controller: 'contactListController'
        //});
        //$routeProvider.when('/Contact/Detail/:id', {
        //    templateUrl: '/Contacts/Detail',
        //    controller: 'contactDetailController'
        //});
        //$routeProvider.when('/Contact/Edit/:id', {
        //    templateUrl: '/Contacts/Edit',
        //    controller: 'contactEditController'
        //});
        //$routeProvider.when('/Contact/Create', {
        //    templateUrl: '/Contacts/Edit',
        //    controller: 'contactEditController'
        //});
        //$routeProvider.when('/Contact/Delete/:id', {
        //    templateUrl: '/Contacts/Detail',
        //    controller: 'deleteController',
        //    isDeleteRequested: true
        //});
        //$routeProvider.when('/Contact/Jaf', {
        //	templateUrl: '/Contacts/Index',
        //	controller: 'contactCeoController'
        //});
        //$routeProvider.when('/Account/Index', {
        //	templateUrl: '/Accounts/Index',
        //	controller: 'accountListController'
        //});
        //$routeProvider.when('/Account/Detail/:id', {
        //	templateUrl: '/Accounts/Detail',
        //	controller: 'accountDetailController'
        //});
        //$routeProvider.when('/Account/Edit/:id', {
        //	templateUrl: '/Accounts/Edit',
        //	controller: 'accountEditController'
        //});
        //$routeProvider.when('/Account/Create', {
        //	templateUrl: '/Accounts/Edit',
        //	controller: 'accountEditController'
        //});
        //$routeProvider.when('/Account/Delete/:id', {
        //	templateUrl: '/Accounts/Detail',
        //	controller: 'accountDeleteController',
        //	isDeleteRequested: true
        //});
        //$routeProvider.when('/Organization/Index', {
        //	templateUrl: '/Organizations/Index',
        //	controller: 'organizationListController'
        //});
        //$routeProvider.when('/Organization/Detail/:id', {
        //	templateUrl: '/Organizations/Detail',
        //	controller: 'organizationDetailController'
        //});
        //$routeProvider.when('/Organization/Edit/:id', {
        //	templateUrl: '/Organizations/Edit',
        //	controller: 'organizationEditController'
        //});
        //$routeProvider.when('/Organization/Create', {
        //	templateUrl: '/Organizations/Edit',
        //	controller: 'organizationEditController'
        //});
        //$routeProvider.when('/Organization/Delete/:id', {
        //    templateUrl: '/Organizations/Detail',
        //    controller: 'organizationDeleteController',
        //    isDeleteRequested: true
        //});
        //$routeProvider.when('/Home/Logout', {
        //    templateUrl: '/Home/Logout',
        //    controller: 'logoutController'
        //});
        //$routeProvider.when('/Reports/ActivityReport',{
        //    templateUrl: '/Reports/ActivityReport',
        //    controller: 'activityReportController'
        //});
        //$routeProvider.when('/Reports/UpdateContactReport', {
        //    templateUrl: '/Reports/UpdateContactReport',
        //    controller: 'updateContactReportController'
        //});
        //$routeProvider.when('/Configurations/Edit', {
        //    templateUrl: '/Configurations/Edit',
        //    controller: 'configurationController'
        //});
        //$routeProvider.when('/Import/Index', {
        //	templateUrl: '/Import/Index',
        //	controller: 'importController'
        //});
        //$routeProvider.when('/Import/ResendEmailToContacts', {
        //    templateUrl: '/Import/ResendEmailToContacts',
        //    controller: 'resendController'
        //});
        //$routeProvider.when('/Calendar/Index', {
        //	templateUrl: '/Calendar/Index',
        //	controller: 'calendarIndexController'
        //});
        //$routeProvider.when('/Calendar/Global', {
        //	templateUrl: '/Views/Calendar/Global.html',
        //	controller: 'calendarGlobalController'
        //});
        //$routeProvider.when('/Calendar/Weekly', {
        //	templateUrl: '/Calendar/Weekly',
        //	controller: 'calendarWeeklyController'
        //});
        //$routeProvider.when('/Calendar/Create', {
        //	templateUrl: '/Calendar/Edit',
        //	controller: 'calendarEditController'
        //});
        //$routeProvider.when('/Calendar/Edit/:id', {
        //	templateUrl: '/Calendar/Edit',
        //	controller: 'calendarEditController'
        //});
        //$routeProvider.when('/Calendar/Search/:id', {
        //    templateUrl: '/Calendar/Search',
        //    controller: 'calendarSearchController'
        //});
        //$routeProvider.when('/Calendar/Search', {
        //	templateUrl: '/Calendar/Search',
        //	controller: 'calendarSearchController'
        //});
        //$routeProvider.when('/Exam/Index', {
        //	templateUrl: '/Exam/Index',
        //	controller: 'examIndexController'
        //});
        //$routeProvider.when('/Exam/Create', {
        //	templateUrl: '/Exam/Edit',
        //	controller: 'examEditController'
        //});
        //$routeProvider.when('/Exam/Edit/:id', {
        //	templateUrl: '/Exam/Edit',
        //	controller: 'examEditController'
        //});
        $routeProvider.otherwise({
        	redirectTo: '/'
        });
        $locationProvider.html5Mode(false);

        $("#menu #submenu").mouseleave(function() {
            $(this).removeClass("unhovered");
        });
    }])
    .controller('RootController', ['$scope', '$route', '$routeParams', '$location', function ($scope, $route, $routeParams, $location) {
        $scope.$on('$routeChangeSuccess', function (e, current, previous) {
            $scope.activeViewPath = $location.path();
        });
    }])
    // Not sure why there's 2 required names
    .directive('requiredname', function () {
        return {
            require: '?ngModel',
            link: function (scope, elm, attr, ctrl) {
                if (!ctrl) return;
                attr.required = true; // force truthy in case we are on non input element

                var validator = function(value) {
                    if (attr.required && (typeof value == 'undefined' || value === '' || value === null || value !== value || value === false)) {
                        ctrl.$setValidity('requiredname', false);
                        return;
                    } else {
                        ctrl.$setValidity('requiredname', true);
                        return value;
                    }
                };

                ctrl.$formatters.push(validator);
                ctrl.$parsers.unshift(validator);

                attr.$observe('requiredname', function () {
                    validator(ctrl.$viewValue);
                });
            }
        };
    })
    .directive('requiredname', function () {
        return {
            require: '?ngModel',
            link: function (scope, elm, attr, ctrl) {
                if (!ctrl) return;
                attr.required = true; // force truthy in case we are on non input element

                var validator = function (value) {
                    if (attr.required && (typeof value == 'undefined' || value === '' || value === null || value !== value || value === false)) {
                        ctrl.$setValidity('requiredname', false);
                        return;
                    } else {
                        ctrl.$setValidity('requiredname', true);
                        return value;
                    }
                };

                ctrl.$formatters.push(validator);
                ctrl.$parsers.unshift(validator);

                attr.$observe('requiredname', function () {
                    validator(ctrl.$viewValue);
                });

                scope.$watch('validationSwitch', function () {
                    validator($("[name='" + ctrl.$name + "']").val());
                });
            }
        };
    })
    .directive('minimumlength', function () {
        return {
            require: '?ngModel',
            link: function (scope, elm, attr, ctrl) {
                if (!ctrl) return;
                attr.required = true; // force truthy in case we are on non input element

                var minlength = parseInt(attr.minimumlength, 10);
                var validator = function (value) {
                    if (typeof value != 'undefined' && value !== '' && value !== null && value.length < minlength) {
                        ctrl.$setValidity('minimumlength', false);
                        return value;
                    }
                    else {
                        ctrl.$setValidity('minimumlength', true);
                        return value;
                    }
                };

                ctrl.$formatters.push(validator);
                ctrl.$parsers.unshift(validator);

                attr.$observe('minimumlength', function () {
                    validator($("[name='" + ctrl.$name + "']").val());
                });
            }
        };
    })
    .directive('maximumlength', function () {
        return {
            require: '?ngModel',
            link: function (scope, elm, attr, ctrl) {
                if (!ctrl) return;
                attr.required = true; // force truthy in case we are on non input element

                var maxlength = parseInt(attr.maximumlength, 10);
                var validator = function (value) {
                    if (typeof value != 'undefined' && value !== '' && value !== null && value.length > maxlength) {
                        ctrl.$setValidity('maximumlength', false);
                        return value;
                    } else {
                        ctrl.$setValidity('maximumlength', true);
                        return value;
                    }
                };

                ctrl.$formatters.push(validator);
                ctrl.$parsers.unshift(validator);

                attr.$observe('maximumlength', function () {
                    validator($("[name='" + ctrl.$name + "']").val());
                });
            }
        };
    })
    .directive('checkurl', function () {
        return {
            require: '?ngModel',
            link: function (scope, elm, attr, ctrl) {
                if (!ctrl) return;
                attr.required = true; // force truthy in case we are on non input element

                var validator = function (value) {
                	var regexp =  /^(((ht|f)tp(s?))\:\/\/)?(www.|[a-zA-Z].)[a-zA-Z0-9\-\.]+\.(com|edu|gov|mil|net|org|biz|info|name|museum|us|ca|uk|gob|mx)(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\;\?\'\\\+&amp;%\$#\=~_\-]+))*$/;
                    if (typeof value != 'undefined' && value !== '' && value !== null) {
                        if (regexp.test(value)) {
                            ctrl.$setValidity('checkurl', true);
                        }
                        else {
                            ctrl.$setValidity('checkurl', false);
                        }
                        return value;
                    } else {
                        ctrl.$setValidity('checkurl', true);
                        return value;
                    }
                };

                ctrl.$formatters.push(validator);
                ctrl.$parsers.unshift(validator);

                attr.$observe('checkurl', function () {
                    validator(ctrl.$viewValue);
                });
            }
        };
    })
    .directive('checkemail', function () {
        return {
            require: '?ngModel',
            link: function (scope, elm, attr, ctrl) {
                if (!ctrl) return;
                attr.required = true; // force truthy in case we are on non input element

                var validator = function (value) {
                    var regexp = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
                    if (typeof value != 'undefined' && value !== '' && value !== null) {
                        if (regexp.test(value)) {
                            ctrl.$setValidity('checkemail', true);
                        }
                        else {
                            ctrl.$setValidity('checkemail', false);
                        }
                        return value;
                    } else {
                        ctrl.$setValidity('checkemail', true);
                        return value;
                    }
                };

                ctrl.$formatters.push(validator);
                ctrl.$parsers.unshift(validator);

                attr.$observe('checkemail', function () {
                    validator(ctrl.$viewValue);
                });
            }
        };
    })
	.directive('checkdate', function () {
		return {
			require: '?ngModel',
			link: function (scope, elm, attr, ctrl) {
				if (!ctrl) return;
				attr.required = true; // force truthy in case we are on non input element

				var validator = function (value) {
					if (typeof value == 'string' && value !== '') {
						var date = parseDate(value);
						
						if (toString.apply(date) == '[object Date]') {
							setValidity(true);
						}
						else {
							setValidity(false);
						}

						return date;
					} else {
						setValidity(true);
						return value;
					}

					function parseDate(input) {
						var parts = input.match(/(\d+)/g);

						if (parts && parts.length >= 3) {
							var beginWithYear = parts[0].length == 4,
								year = beginWithYear ? parts[0] : parts[2],
								month = (parts[1] - 1),
								day = beginWithYear ? parts[2] : parts[0];
							
							return new Date(year, month, day, 12, 0, 0);
						} else {
							return null;
						}
					}

					function setValidity(isValid) {
						ctrl.$valid = isValid;
						ctrl.$invalid = !isValid;
					}
				};

				ctrl.$formatters.push(validator);
				ctrl.$parsers.unshift(validator);

				attr.$observe('checkdate', function () {
					validator(ctrl.$viewValue);
				});
			}
		};
	})
    .directive('ngFocus', function($timeout) {
        return function(scope, elem, attrs) {
            scope.$watch(attrs.ngFocus, function(newval) {
                if (newval) {
                    $timeout(function() {
                        elem[0].focus();
                    }, 0, false);
                }
            });
        };
    })
    .directive('ngBlur', function () {
        return function (scope, elem, attrs) {
            elem.bind('blur', function () {
                scope.$apply(attrs.ngBlur);
            });
        };
    });