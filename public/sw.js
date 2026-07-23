const cacheName = 'amikom-event-v1';
const assets = ['/', '/css/app.css', '/js/app.js'];

self.addEventListener('install', e => {
    e.waitUntil(caches.open(cacheName).then(cache => cache.addAll(assets)));
});

self.addEventListener('fetch', e => {
    e.respondWith(caches.match(e.request).then(cachedResponse => cachedResponse || fetch(e.request)));
});