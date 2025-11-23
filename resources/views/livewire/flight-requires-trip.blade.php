<div class="flex h-full w-full items-center justify-center">
    <div class="max-w-md rounded-xl border border-neutral-200 bg-white p-8 text-center dark:border-neutral-700 dark:bg-zinc-800">
        <div class="mb-4 flex justify-center">
            <svg class="h-16 w-16 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <flux:heading size="lg" class="mb-2 text-gray-900 dark:text-white">Employee Number Required</flux:heading>
        <flux:text class="mb-6 text-gray-600 dark:text-gray-400">
            Please enter your employee number in the Trip step before configuring flight details.
        </flux:text>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-600 transition-colors">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Go to Trip
        </a>
    </div>
</div>
