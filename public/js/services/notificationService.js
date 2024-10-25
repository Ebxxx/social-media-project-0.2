angular.module('socialMediaApp')
    .service('NotificationService', function($http) {
        this.fetchNotifications = function() {
            return $http.get('/api/notifications');
        };

        this.markAsRead = function(notificationId) {
            return $http.post('/api/notifications/' + notificationId + '/markAsRead');
        };

        this.deleteNotification = function(notificationId) {
            return $http.delete('/api/notifications/' + notificationId);
        };
    });
