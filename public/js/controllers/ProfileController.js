angular.module('socialMediaApp')
    .controller('ProfileController', function($scope, $http, $timeout) {
        // Initialize user data
        $scope.profile = {
            name: '',
            bio: '',
            profile_picture: null // For file upload
        };

        $scope.successMessage = '';
        $scope.showSuccessMessage = false;
        $scope.editMode = false; // Add this line to control edit mode

        // Fetch user profile data
        $http.get('/api/profile') // Ensure this endpoint is correctly set up
            .then(function(response) {
                $scope.profile.name = response.data.name;
                $scope.profile.bio = response.data.bio;
                $scope.profile.profile_picture = response.data.profile_picture;
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
                // Update the profile data after successful update
                $scope.profile.name = response.data.name;
                $scope.profile.bio = response.data.bio;
                $scope.profile.profile_picture = response.data.profile_picture;

                // Show success message
                $scope.successMessage = 'Profile updated successfully!';
                $scope.showSuccessMessage = true;

                // Hide the success message after 3 seconds
                $timeout(function() {
                    $scope.showSuccessMessage = false;
                }, 3000);

                $scope.editMode = false; // Close edit mode after successful update
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

        // Function to toggle edit mode
        $scope.toggleEditMode = function() {
            $scope.editMode = !$scope.editMode;
        };

        // Function to cancel edit mode
        $scope.cancelEdit = function() {
            $scope.editMode = false;
            // Reset any changes made to the profile
            $http.get('/api/profile').then(function(response) {
                $scope.profile.name = response.data.name;
                $scope.profile.bio = response.data.bio;
                $scope.profile.profile_picture = response.data.profile_picture;
            });
        };
    });