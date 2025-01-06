// angular.module('socialMediaApp')
//     .controller('NotificationController', function($scope, NotificationService) {
//         var $ctrl = this;
//         $ctrl.notifications = [];
//         $ctrl.unreadCount = 0;
//         $ctrl.showNotifications = false;

//         $ctrl.fetchNotifications = function() {
//             NotificationService.fetchNotifications().then(function(response) {
//                 $ctrl.notifications = response.data;
//                 $ctrl.unreadCount = $ctrl.notifications.filter(n => !n.is_read).length;
//             }, function(error) {
//                 console.error('Error fetching notifications', error);
//             });
//         };

//         $ctrl.markAsRead = function(notification) {
//             NotificationService.markAsRead(notification.id).then(function() {
//                 notification.is_read = true;
//                 $ctrl.unreadCount = Math.max(0, $ctrl.unreadCount - 1);
//             }, function(error) {
//                 console.error('Error marking notification as read', error);
//             });
//         };

//         $ctrl.deleteNotification = function(notification) {
//             NotificationService.deleteNotification(notification.id).then(function() {
//                 $ctrl.notifications = $ctrl.notifications.filter(n => n.id !== notification.id);
//                 if (!notification.is_read) {
//                     $ctrl.unreadCount = Math.max(0, $ctrl.unreadCount - 1);
//                 }
//             }, function(error) {
//                 console.error('Error deleting notification', error);
//             });
//         };

//         $ctrl.toggleNotifications = function() {
//             $ctrl.showNotifications = !$ctrl.showNotifications;
//             if ($ctrl.showNotifications) {
//                 $ctrl.fetchNotifications();
//             }
//         };

//         // Initialize
//         $ctrl.fetchNotifications();
//     });

angular.module('socialMediaApp')
  .controller('NotificationController', function($scope, NotificationService) {
    var $ctrl = this;
    $ctrl.notifications = [];
    $ctrl.unreadCount = 0;
    $ctrl.showNotifications = false;

    $ctrl.fetchNotifications = function() {
      NotificationService.fetchNotifications().then(function(response) {
        $ctrl.notifications = response.data;
        $ctrl.unreadCount = $ctrl.notifications.filter(n => !n.is_read).length;
      }, function(error) {
        console.error('Error fetching notifications', error);
      });
    };

    $ctrl.markAsRead = function(notification) {
      NotificationService.markAsRead(notification.id).then(function() {
        notification.is_read = true;
        $ctrl.unreadCount = Math.max(0, $ctrl.unreadCount - 1);
      }, function(error) {
        console.error('Error marking notification as read', error);
      });
    };

    $ctrl.deleteNotification = function(notification) {
      NotificationService.deleteNotification(notification.id).then(function() {
        $ctrl.notifications = $ctrl.notifications.filter(n => n.id !== notification.id);
        if (!notification.is_read) {
          $ctrl.unreadCount = Math.max(0, $ctrl.unreadCount - 1);
        }
      }, function(error) {
        console.error('Error deleting notification', error);
      });
    };

    $ctrl.toggleNotifications = function() {
      $ctrl.showNotifications = !$ctrl.showNotifications;
      if ($ctrl.showNotifications) {
        $ctrl.fetchNotifications();
      }
    };

    // Initialize
    $ctrl.fetchNotifications();

    // Listen for real-time updates
    NotificationService.onNewNotification().then(null, null, function(notification) {
      $ctrl.notifications.unshift(notification);
      $ctrl.unreadCount++;
    });
  });