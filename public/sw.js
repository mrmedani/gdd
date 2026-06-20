const CACHE_NAME = 'chronorex-v3';

const PRECACHE_URLS = [
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/offline',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE_URLS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // CDN cache-first: Google Fonts, Chart.js, SweetAlert2
    if (url.origin === 'https://fonts.googleapis.com' || url.origin === 'https://fonts.gstatic.com' || url.href.includes('cdn.jsdelivr.net/npm/chart.js') || url.href.includes('cdn.jsdelivr.net/npm/sweetalert2')) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                    return response;
                }).catch(() => cached);
            })
        );
        return;
    }

    // Only handle same-origin requests
    if (url.origin !== location.origin) return;

    // For build assets (CSS/JS), use cache-first
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(
            caches.match(request).then(cached => {
                return cached || fetch(request).then(response => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                    return response;
                });
            })
        );
        return;
    }

    // For API/Livewire requests, use network-only
    if (request.method === 'POST' || url.pathname.startsWith('/livewire/')) {
        event.respondWith(fetch(request).catch(() => {
            return new Response(JSON.stringify({ error: 'offline' }), {
                status: 503,
                headers: { 'Content-Type': 'application/json' },
            });
        }));
        return;
    }

    // For navigation requests, use network-first with URL-specific cache fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).then(response => {
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                return response;
            }).catch(() => {
                return caches.match(request).then(cached => {
                    if (cached) return cached;
                    return caches.match('/offline');
                });
            })
        );
        return;
    }

    // For other requests (images, etc.), stale-while-revalidate
    event.respondWith(
        caches.match(request).then(cached => {
            const fetchPromise = fetch(request).then(response => {
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                return response;
            }).catch(() => cached);
            return cached || fetchPromise;
        })
    );
});
