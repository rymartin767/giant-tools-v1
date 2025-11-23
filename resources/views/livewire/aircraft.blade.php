<div class="h-full w-full">
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">Aircraft Management</flux:heading>
        <flux:text>Select and manage your aircraft configuration.</flux:text>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('message') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Aircraft Configuration Card -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Aircraft Configuration</flux:heading>

            <form wire:submit.prevent="saveAircraftConfiguration" class="space-y-6">
                <!-- Aircraft Registration -->
                <div>
                    <label class="mb-2 block text-base font-medium text-gray-700 dark:text-gray-300">Aircraft Registration</label>
                    <select wire:model.live="selectedAirframe"
                            class="w-full rounded-xl border border-gray-300 px-4 py-4 text-base focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white appearance-none bg-white dark:bg-zinc-700">
                        <option value="">Select Aircraft Registration</option>
                        @foreach($this->airframeOptions as $registration => $registrationDisplay)
                            <option value="{{ $registration }}">{{ $registrationDisplay }}</option>
                        @endforeach
                    </select>
                    @error('selectedAirframe')
                        <span class="mt-2 block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Engine Type -->
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <label class="text-base font-medium text-gray-700 dark:text-gray-300">Engine Type</label>
                        @if($engines && $selectedAirframe)
                            <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">Auto-detected</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($engineOptions as $option)
                            <label class="relative flex cursor-pointer items-center justify-center rounded-xl border-2 p-5 transition-all duration-200 touch-manipulation {{ $engines === $option ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 hover:border-gray-400 dark:border-zinc-600 dark:hover:border-zinc-500' }}">
                                <input type="radio" wire:model.live="engines" value="{{ $option }}" class="sr-only">
                                <span class="text-xl font-bold {{ $engines === $option ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $option }}</span>
                                @if($engines === $option)
                                    <svg class="absolute right-3 top-3 h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @error('engines')
                        <span class="mt-2 block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Configuration -->
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <label class="text-base font-medium text-gray-700 dark:text-gray-300">Configuration</label>
                        @if($config && $selectedAirframe)
                            <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">Auto-detected</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($configOptions as $option)
                            <label class="relative flex cursor-pointer items-center justify-center rounded-xl border-2 p-5 transition-all duration-200 touch-manipulation {{ $config === $option ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 hover:border-gray-400 dark:border-zinc-600 dark:hover:border-zinc-500' }}">
                                <input type="radio" wire:model.live="config" value="{{ $option }}" class="sr-only">
                                <span class="text-xl font-bold {{ $config === $option ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $option }}</span>
                                @if($config === $option)
                                    <svg class="absolute right-3 top-3 h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @error('config')
                        <span class="mt-2 block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Supernumeraries -->
                <div class="flex items-center justify-between rounded-xl bg-gray-50 p-5 dark:bg-zinc-700">
                    <div>
                        <span class="text-base font-medium text-gray-700 dark:text-gray-300">Supernumeraries</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Are there additional crew members?</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center touch-manipulation">
                        <input type="checkbox" wire:model.live="supernumeraries" class="peer sr-only">
                        <div class="peer relative h-8 w-14 rounded-full bg-gray-200 after:absolute after:left-[3px] after:top-[3px] after:h-[26px] after:w-[26px] after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-6 peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-blue-800"></div>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <flux:button type="submit" variant="primary" class="w-full sm:flex-1 py-3">
                        Save Configuration
                    </flux:button>

                    @if($selectedAirframe)
                        <flux:button type="button" wire:click="clearAirframe" variant="ghost" class="w-full sm:w-auto py-3">
                            Clear
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Current Aircraft Details Card -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Current Aircraft</flux:heading>

            @if($selectedAirframe)
                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                        <span class="text-base text-gray-600 dark:text-gray-400">Registration:</span>
                        <span class="font-semibold text-base text-gray-900 dark:text-white break-all">{{ $selectedAirframe }}</span>
                    </div>

                    @if($engines)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                            <span class="text-base text-gray-600 dark:text-gray-400">Engine Type:</span>
                            <span class="font-semibold text-base text-gray-900 dark:text-white">{{ $engines }}</span>
                        </div>
                    @endif

                    @if($config)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                            <span class="text-base text-gray-600 dark:text-gray-400">Configuration:</span>
                            <span class="font-semibold text-base text-gray-900 dark:text-white">{{ $config }}</span>
                        </div>
                    @endif

                    @if($supernumeraries)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                            <span class="text-base text-gray-600 dark:text-gray-400">Supernumeraries:</span>
                            <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">Yes</span>
                        </div>
                    @endif

                    @if($airframeData)
                        @if(isset($airframeData['name']))
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                                <span class="text-base text-gray-600 dark:text-gray-400">Aircraft Name:</span>
                                <span class="font-semibold text-base text-gray-900 dark:text-white break-words">{{ $airframeData['name'] }}</span>
                            </div>
                        @endif

                        @if(isset($airframeData['type']))
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                                <span class="text-base text-gray-600 dark:text-gray-400">Type:</span>
                                <span class="font-semibold text-base text-gray-900 dark:text-white break-words">{{ $airframeData['type'] }}</span>
                            </div>
                        @endif

                        @if(isset($airframeData['manufacturer']))
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                                <span class="text-base text-gray-600 dark:text-gray-400">Manufacturer:</span>
                                <span class="font-semibold text-base text-gray-900 dark:text-white break-words">{{ $airframeData['manufacturer'] }}</span>
                            </div>
                        @endif

                        @if(isset($airframeData['model']))
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                                <span class="text-base text-gray-600 dark:text-gray-400">Model:</span>
                                <span class="font-semibold text-base text-gray-900 dark:text-white break-words">{{ $airframeData['model'] }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="flex h-48 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-zinc-600">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No aircraft selected</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
