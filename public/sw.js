const CACHE_NAME = 'flight-checklist-v1';
const urlsToCache = [
  '/',
  '/manifest.json',
  '/css/app.css',
  '/js/app.js',
  // Add other critical assets
];

// Install event - cache resources
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
  // Only handle GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Return cached version or fetch from network
        return response || fetch(event.request).then(fetchResponse => {
          // Save new resources to cache
          if (fetchResponse && fetchResponse.status === 200) {
            const responseClone = fetchResponse.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(event.request, responseClone);
            });
          }
          return fetchResponse;
        });
      })
      .catch(() => {
        // Fallback for offline navigation
        if (event.request.destination === 'document') {
          return caches.match('/');
        }
      })
  );
});

// Background sync for checklist data
self.addEventListener('sync', event => {
  if (event.tag === 'checklist-sync') {
    event.waitUntil(syncChecklistData());
  }
});

async function syncChecklistData() {
  try {
    // Get pending sync data from IndexedDB
    const pendingData = await getPendingSync();

    if (pendingData.length > 0) {
      for (const data of pendingData) {
        await fetch('/livewire/update', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(data)
        });
      }

      // Clear pending sync data
      await clearPendingSync();
    }
  } catch (error) {
    console.error('Background sync failed:', error);
  }
}

// Helper functions for IndexedDB operations
async function getPendingSync() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('ChecklistDB', 1);

    request.onsuccess = () => {
      const db = request.result;
      const transaction = db.transaction(['pending'], 'readonly');
      const store = transaction.objectStore('pending');
      const getAllRequest = store.getAll();

      getAllRequest.onsuccess = () => resolve(getAllRequest.result);
      getAllRequest.onerror = () => reject(getAllRequest.error);
    };

    request.onerror = () => reject(request.error);
  });
}

async function clearPendingSync() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('ChecklistDB', 1);

    request.onsuccess = () => {
      const db = request.result;
      const transaction = db.transaction(['pending'], 'readwrite');
      const store = transaction.objectStore('pending');
      const clearRequest = store.clear();

      clearRequest.onsuccess = () => resolve();
      clearRequest.onerror = () => reject(clearRequest.error);
    };

    request.onerror = () => reject(request.error);
  });
}