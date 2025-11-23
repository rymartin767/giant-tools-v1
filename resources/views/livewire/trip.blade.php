<div class="h-full w-full">
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">Employee Identification</flux:heading>
        <flux:text>Enter your employee number to begin.</flux:text>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Employee Number Form -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Employee Number</flux:heading>

            <form wire:submit.prevent="saveEmployeeNumber" class="space-y-4">
                <div>
                    <flux:field>
                        <flux:label>Employee Number</flux:label>
                        <flux:input
                            wire:model="employeeNumber"
                            type="text"
                            placeholder="Enter your employee number"
                            required
                        />
                        @error('employeeNumber')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        Save Employee Number
                    </flux:button>

                    @if($employeeNumber)
                        <flux:button type="button" wire:click="clearEmployeeNumber" variant="danger">
                            Clear
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Status Card -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Current Status</flux:heading>

            <div class="space-y-4">
                @if($employeeNumber)
                    <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-800 dark:text-green-200">Employee number saved</span>
                        </div>
                        <p class="mt-2 text-lg font-semibold text-green-900 dark:text-green-100">{{ $employeeNumber }}</p>
                    </div>

                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            You can now proceed to configure your flight details.
                        </p>
                    </div>
                @else
                    <div class="flex h-64 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-zinc-600">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Enter your employee number to get started</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
