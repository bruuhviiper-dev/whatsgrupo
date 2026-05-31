// Service Worker para Web Push Notifications
self.addEventListener('push', function(event) {
    if (!event.data) return;
    
    let data = {};
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: 'WhatsGrupos',
            body: event.data.text(),
            url: '/'
        };
    }
    
    const options = {
        body: data.body,
        icon: '/images/icon-192.png',
        badge: '/images/badge-72.png',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/' },
        actions: [
            { action: 'open', title: 'Ver Grupos' },
            { action: 'close', title: 'Fechar' }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    if (event.action === 'close') return;
    
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});
