// Import Firebase scripts
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyA-JUGF6cA5c_c_CMvh2X-deEyeP_WlxWk",
    authDomain: "kandura-store-notifications.firebaseapp.com",
    projectId: "kandura-store-notifications",
    storageBucket: "kandura-store-notifications.firebasestorage.app",
    messagingSenderId: "845537116493",
    appId: "1:845537116493:web:1e31619cb1f4d5e1863704"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[SW] Background message:', payload);

    const notificationTitle = payload.notification?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        data: {
            url: getUrlFromNotification(payload)
        }
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked:', event);

    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if dashboard is already open
                for (let client of clientList) {
                    if (client.url.includes('/dashboard') && 'focus' in client) {
                        return client.focus().then(() => client.navigate(urlToOpen));
                    }
                }
                // Open new window
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Helper function to determine URL based on notification type
function getUrlFromNotification(payload) {
    const data = payload.data || {};
    const type = data.type;

    if (type === 'new_order_admin' || type === 'new_order_user') {
        const orderId = data.order_id;
        return orderId ? `/admin/orders/${orderId}` : '/admin/orders';
    }

    if (type === 'design_created') {
        const designId = data.design_id;
        return designId ? `/admin/designs/${designId}` : '/admin/designs';
    }

    if (type === 'order_status_changed') {
        const orderId = data.order_id;
        return orderId ? `/admin/orders/${orderId}` : '/admin/orders';
    }

    return '/dashboard';
}
