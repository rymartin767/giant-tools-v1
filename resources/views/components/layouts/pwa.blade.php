<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <!-- Mobile Header -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />
        </flux:header>

        <div class="flex min-h-screen">
            <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Flight Planning')" class="grid">
                    <flux:navlist.item icon="map" :href="route('dashboard', ['view' => 'trip'])" :current="request()->get('view') === 'trip'" wire:navigate>{{ __('Trip') }}</flux:navlist.item>
                    <flux:navlist.item icon="globe-americas" :href="route('dashboard', ['view' => 'flight'])" :current="request()->get('view') === 'flight'" wire:navigate>{{ __('Flight') }}</flux:navlist.item>
                    <flux:navlist.item icon="paper-airplane" :href="route('dashboard', ['view' => 'aircraft'])" :current="request()->get('view') === 'aircraft'" wire:navigate>{{ __('Aircraft') }}</flux:navlist.item>
                    <flux:navlist.item icon="shield-check" :href="route('dashboard')" :current="request()->routeIs('dashboard') && !request()->has('view')" wire:navigate>{{ __('Checklist') }}</flux:navlist.item>
                    <flux:navlist.item icon="book-open-text" :href="route('dashboard', ['view' => 'briefing'])" :current="request()->get('view') === 'briefing'" wire:navigate>{{ __('Briefing') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

            <div class="flex-1 flex flex-col min-h-screen p-6">
                {{ $slot }}
            </div>
        </div>

        <!-- Connection Status Indicator -->
        <div id="connection-status"
             class="fixed bottom-4 left-4 right-4 mx-auto max-w-sm bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg text-center text-sm transition-transform transform translate-y-full"
             style="display: none;">
            <span id="status-text">You're offline. Changes will sync when connection is restored.</span>
        </div>

        @fluxScripts

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registered successfully:', registration.scope);

                        // Register for background sync
                        if ('sync' in window.ServiceWorkerRegistration.prototype) {
                            registration.sync.register('checklist-sync');
                        }
                    })
                    .catch(error => {
                        console.log('ServiceWorker registration failed:', error);
                    });
            }

            // Install prompt - use window scope to avoid redeclaration on navigation
            if (!window.deferredPrompt) {
                window.deferredPrompt = null;
            }

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                window.deferredPrompt = e;

                // Show install button or banner
                const installButton = document.getElementById('install-button');
                if (installButton) {
                    installButton.style.display = 'block';
                    installButton.addEventListener('click', () => {
                        window.deferredPrompt.prompt();
                        window.deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('User accepted the install prompt');
                            }
                            window.deferredPrompt = null;
                        });
                    });
                }
            });

            // Connection Status
            function updateConnectionStatus() {
                const statusDiv = document.getElementById('connection-status');
                const statusText = document.getElementById('status-text');

                if (navigator.onLine) {
                    statusDiv.style.display = 'none';
                    statusDiv.classList.add('translate-y-full');
                } else {
                    statusDiv.style.display = 'block';
                    statusDiv.classList.remove('translate-y-full');
                    statusText.textContent = "You're offline. Changes will sync when connection is restored.";
                }
            }

            window.addEventListener('online', updateConnectionStatus);
            window.addEventListener('offline', updateConnectionStatus);
            updateConnectionStatus(); // Initial check

            // Initialize IndexedDB for offline storage
            function initIndexedDB() {
                const request = indexedDB.open('ChecklistDB', 1);

                request.onupgradeneeded = (event) => {
                    const db = event.target.result;

                    // Create stores
                    if (!db.objectStoreNames.contains('checklist')) {
                        const checklistStore = db.createObjectStore('checklist', { keyPath: 'id' });
                        checklistStore.createIndex('userId', 'userId', { unique: false });
                    }

                    if (!db.objectStoreNames.contains('pending')) {
                        db.createObjectStore('pending', { keyPath: 'id', autoIncrement: true });
                    }
                };

                request.onsuccess = () => {
                    // IndexedDB initialized successfully - silent success
                };

                request.onerror = () => {
                    console.error('IndexedDB initialization failed');
                };
            }

            initIndexedDB();
        </script>

    </body>
</html>