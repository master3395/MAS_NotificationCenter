/* MAS Notification Center: minimal push handler for published mas_nc_sw.js */
self.addEventListener('push', function (event) {
  var payload = { title: 'Notice', body: '' };
  if (event.data) {
    try {
      var parsed = event.data.json();
      if (parsed && parsed.title) {
        payload.title = String(parsed.title);
      }
      if (parsed && parsed.body) {
        payload.body = String(parsed.body);
      }
    } catch (e) {
      payload.body = event.data.text();
    }
  }
  event.waitUntil(self.registration.showNotification(payload.title, {
    body: payload.body,
    icon: '/modules/MAS_NotificationCenter/images/icon-192.png'
  }));
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  event.waitUntil(clients.openWindow('/'));
});
