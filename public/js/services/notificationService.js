// angular.module('socialMediaApp')
//     .service('NotificationService', function($http) {
//         this.fetchNotifications = function() {
//             return $http.get('/api/notifications');
//         };

//         this.markAsRead = function(notificationId) {
//             return $http.post('/api/notifications/' + notificationId + '/markAsRead');
//         };

//         this.deleteNotification = function(notificationId) {
//             return $http.delete('/api/notifications/' + notificationId);
//         };
//     });


angular.module('socialMediaApp')
  .service('NotificationService', function($http, $rootScope, $q) {
    var pusher = new Pusher('71cd8afe3a9d0d2b4c29', {
      cluster: 'ap1'
    });
    var channel = pusher.subscribe('notifications');

    this.fetchNotifications = function() {
      return $http.get('/api/notifications');
    };

    this.markAsRead = function(notificationId) {
      return $http.post('/api/notifications/' + notificationId + '/markAsRead');
    };

    this.deleteNotification = function(notificationId) {
      return $http.delete('/api/notifications/' + notificationId);
    };

    var deferred = $q.defer();
    channel.bind('new-notification', function(data) {
      $rootScope.$apply(function() {
        deferred.notify(data);
      });
    });

    this.onNewNotification = function() {
      return deferred.promise;
    };
  });
