angular.module('socialMediaApp')
    .controller('ProfileController', function($scope, $http) {
        // Initialize user data
        $scope.profile = {
            name: '',
            bio: '',
            profile_picture: null // For file upload
        };

        // Fetch user profile data
        $http.get('/api/profile') // Ensure this endpoint is correctly set up
            .then(function(response) {
                $scope.profile.name = response.data.name; // Assuming the response has name
                $scope.profile.bio = response.data.bio; // Assuming the response has bio
                $scope.profile.profile_picture = response.data.profile_picture; // Assuming the response has profile_picture
            })
            .catch(function(error) {
                console.error('Error fetching profile:', error);
            });

            // Update function
            $scope.updateProfile = function() {
                let formData = new FormData();
                formData.append('name', $scope.profile.name);
                formData.append('bio', $scope.profile.bio);

                if ($scope.profile.profile_picture) {
                    formData.append('profile_picture', $scope.profile.profile_picture);
                }

                // POST request to Laravel API for updating the profile
                $http.post('/api/profile', formData, {
                    headers: {
                        'Content-Type': undefined // Let the browser set the correct headers for multipart/form-data
                    }
                }).then(function(response) {
                    alert('Profile updated successfully!');
                    // Update the profile data after successful update
                    $scope.profile.name = response.data.name;
                    $scope.profile.bio = response.data.bio;
                    $scope.profile.profile_picture = response.data.profile_picture; // Update profile picture on success
                }).catch(function(error) {
                    console.error('Error updating profile:', error);
                    alert('Failed to update profile');
                });
            };


        // Function to handle file input changes
        $scope.onFileChange = function(element) {
            $scope.$apply(() => {
                $scope.profile.profile_picture = element.files[0];
            });
        };
    });
