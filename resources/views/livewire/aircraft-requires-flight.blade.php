<div class="h-full w-full flex items-center justify-center bg-gray-50 dark:bg-zinc-900">
    <div class="text-center max-w-md mx-auto p-8">
        <div class="mb-6">
            <svg class="mx-auto h-16 w-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>

        <flux:heading size="xl" class="mb-4">Flight Configuration Required</flux:heading>

        <flux:text class="mb-6 text-gray-600 dark:text-gray-400">
            Before you can configure aircraft details, you need to complete your flight configuration with departure and arrival airports.
        </flux:text>

        <a href="/dashboard?view=flight" wire:navigate class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-semibold transition-colors duration-200">
            Go to Flight Configuration
            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
</div>