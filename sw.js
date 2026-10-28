const VERSION = '__BUILD__';
const CACHE = `himym-${VERSION}`;

const CORE = [
  './index.html',
  './disclaimer/',
  './how-i-met-your-mother.json',
  './assets/hero.jpg'
];

const EXTERNALS = [
  { url: 'https://cdn.tailwindcss.com/3.4.17', mode: 'no-cors' },
  { url: 'https://cdn.jsdelivr.net/npm/@fontsource-variable/vazirmatn/index.css', mode: 'no-cors' },
  { url: 'https://cdn.jsdelivr.net/npm/@fontsource-variable/vazirmatn/files/vazirmatn-arabic-wght-normal.woff2', mode: 'cors' },
  { url: 'https://cdn.jsdelivr.net/npm/@fontsource-variable/vazirmatn/files/vazirmatn-latin-ext-wght-normal.woff2', mode: 'cors' },
  { url: 'https://cdn.jsdelivr.net/npm/@fontsource-variable/vazirmatn/files/vazirmatn-latin-wght-normal.woff2', mode: 'cors' }
];

const BYPASS_HOSTS = ['webiro.ir', 'giftmond.ir'];

self.addEventListener('install', (event) => {
  event.waitUntil((async () => {
    const cache = await caches.open(CACHE);
    await Promise.all(CORE.map((url) => cache.add(new Request(url, { cache: 'reload' })).catch(() => {})));
    await Promise.all(EXTERNALS.map(async ({ url, mode }) => {
      try {
        const res = await fetch(url, { mode, cache: 'reload' });
        if (res && (res.ok || res.type === 'opaque')) await cache.put(url, res);
      } catch (_) {}
    }));
    await self.skipWaiting();
  })());
});

self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const keys = await caches.keys();
    await Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key)));
    await self.clients.claim();
  })());
});

function shouldBypass(request, url) {
  if (request.method !== 'GET') return true;
  if (request.headers.has('range')) return true;
  if (request.destination === 'video' || request.destination === 'audio') return true;
  return BYPASS_HOSTS.some((host) => url.hostname === host || url.hostname.endsWith('.' + host));
}

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);
  if (shouldBypass(request, url)) return;

  if (request.mode === 'navigate') {
    event.respondWith((async () => {
      const cache = await caches.open(CACHE);
      const cached = await cache.match(request, { ignoreSearch: true });
      const refresh = fetch(request, { cache: 'no-cache' }).then((res) => {
        if (res && res.ok) cache.put(request, res.clone()).catch(() => {});
        return res;
      }).catch(() => null);
      if (cached) {
        event.waitUntil(refresh);
        return cached;
      }
      const fresh = await refresh;
      if (fresh) return fresh;
      return (await cache.match('./index.html')) || Response.error();
    })());
    return;
  }

  event.respondWith((async () => {
    const cache = await caches.open(CACHE);
    const cached = await cache.match(request, { ignoreVary: true });
    const refresh = fetch(request).then((res) => {
      if (res && (res.ok || res.type === 'opaque')) cache.put(request, res.clone()).catch(() => {});
      return res;
    }).catch(() => null);
    if (cached) {
      event.waitUntil(refresh);
      return cached;
    }
    return (await refresh) || Response.error();
  })());
});
