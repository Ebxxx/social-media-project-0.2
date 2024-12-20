angular.module('socialMediaApp', ['ngRoute'])
    .config(function($routeProvider, $httpProvider) {
        $routeProvider
            .when('/', {
                redirectTo: '/login'
            })
            .when('/login', {
                templateUrl: 'templates/login.html',
                controller: 'LoginController'
            })
            .when('/register', {
                templateUrl: 'templates/register.html',
                controller: 'RegisterController'
            })
            .when('/posts', {
                templateUrl: 'templates/posts.html',
                controller: 'PostController'
            })
            .when('/profile', { // Add the profile route here
                templateUrl: 'templates/partial/profile.html',
                controller: 'ProfileController'
            })
            .when('/notification', {
                templateUrl: 'templates/posts.html',
                controller: 'NotificationController'
            })
            .otherwise({
                redirectTo: '/login'
            });

        // Configure XSRF
        $httpProvider.defaults.xsrfCookieName = 'XSRF-TOKEN';
        $httpProvider.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

        // Add authentication interceptor
        $httpProvider.interceptors.push('AuthInterceptor');
    });