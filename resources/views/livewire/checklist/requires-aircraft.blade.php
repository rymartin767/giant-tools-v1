<div class="h-full w-full flex items-center justify-center bg-gray-50 dark:bg-zinc-900">
    <div class="text-center max-w-md mx-auto p-8">
        <div class="mb-6">
            <svg class="mx-auto h-16 w-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>

        <flux:heading size="xl" class="mb-4">Aircraft Configuration Required</flux:heading>

        <flux:text class="mb-6 text-gray-600 dark:text-gray-400">
            Before you can access the checklist, you need to select and configure your aircraft.
        </flux:text>

        <a href="/aircraft" wire:navigate class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-semibold transition-colors duration-200">
            Go to Aircraft Configuration
            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
</div>
