// AmikomEventHub Service Worker v1.0
const CACHE_NAME = 'amikomeventhub-v1';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
];

// ── Install: Pre-cache static assets ──────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch(() => {
                // Ignore cache errors (e.g., cross-origin requests in dev)
            });
        })
    );
    self.skipWaiting();
});

// ── Activate: Clean old caches ─────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

// ── Fetch: Cache-first for static, Network-first for dynamic ───────────────
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Skip non-GET & non-http(s) requests
    if (event.request.method !== 'GET') return;
    if (!url.protocol.startsWith('http')) return;

    // Skip POST/API routes that shouldn't be cached
    const skipPatterns = ['/checkout', '/midtrans', '/checkin/verify', '/review', '/logout'];
    if (skipPatterns.some(p => url.pathname.startsWith(p))) return;

    // Network-first for HTML pages (always fresh)
    if (event.request.headers.get('Accept')?.includes('text/html')) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    return response;
                })
                .catch(() => caches.match(event.request))
        );
        return;
    }

    // Cache-first for CSS/JS/fonts/images
    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;
            return fetch(event.request).then((response) => {
                if (response && response.status === 200 && response.type !== 'opaque') {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return response;
            });
        })
    );
});