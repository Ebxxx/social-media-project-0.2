import { Component, OnInit } from '@angular/core';
import Echo from 'laravel-echo';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  notifications: Notification[] = [];
  unreadCount = 0;

  ngOnInit() {
    this.initRealTimeNotifications();
  }

  initRealTimeNotifications() {
    const echo = new Echo({
      broadcaster: 'pusher',
      key: 'your-pusher-app-key',
      cluster: 'your-pusher-cluster',
      forceTLS: true
    });

    echo.private(`notifications.${this.currentUserId}`)
      .listen('NewNotification', (event: { notification: Notification }) => {
        this.notifications.unshift(event.notification);
        this.unreadCount++;
      });
  }

  get currentUserId() {
    // Implement your own logic to get the current user's ID
    return 123;
  }
}