importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

const firebaseConfig = {
    apiKey: "AIzaSyCHbw1RSF4aKEFYCZAcZjXHkjLYXcLsn6o",
    authDomain: "confideas-2ee27.firebaseapp.com",
    databaseURL: "https://confideas-2ee27-default-rtdb.firebaseio.com",
    projectId: "confideas-2ee27",
    storageBucket: "confideas-2ee27.appspot.com",
    messagingSenderId: "723847871474",
    appId: "1:723847871474:web:2d2419f642f2a3193bc96b",
    measurementId: "G-SJ0GBLV0FW"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.data.title;
    const notificationOptions = {
        body: payload.data.body,
        data: {
            url: payload.data.url // Include the URL in the data
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data.url;
    if (url) {
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
                // Check if there is already a window/tab open with the target URL
                for (let i = 0; i < windowClients.length; i++) {
                    const client = windowClients[i];
                    if (client.url.indexOf(url) >= 0 && 'focus' in client) {
                        return client.focus();
                    }
                }
                // If not, then open the URL in a new window/tab
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
        );
    }
});