angular.module('socialMediaApp')
    .factory('ProfileService', function($http) {
        return {
            getProfile: function() {
                return $http.get('/api/profile');
            },
            updateProfile: function(user) {
                var formData = new FormData();
                formData.append('name', user.name);
                formData.append('bio', user.bio);

                if (user.profile_picture) {
                    formData.append('profile_picture', user.profile_picture);
                }

                return $http.post('/api/profile/update', formData, {
                    headers: { 'Content-Type': undefined },
                    transformRequest: angular.identity
                });
            }
        };
    });
