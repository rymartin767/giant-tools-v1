<div class="h-full w-full">
    @if(!$employeeNumber)
        @livewire('flight-requires-trip')
    @else
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl" class="mb-2">Flight Details</flux:heading>
                    <flux:text>Configure your departure, arrival, and operational parameters.</flux:text>
                </div>
                @if($departureAirport && $arrivalAirport)
                    <div class="rounded-xl bg-gradient-to-r from-gray-600 to-gray-800 px-6 py-3 shadow-lg dark:from-gray-700 dark:to-gray-900">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-lg font-bold text-white">{{ $departureAirport }}</span>
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            <span class="font-mono text-lg font-bold text-white">{{ $arrivalAirport }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('message') }}</p>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
        <!-- Flight Configuration Card -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Flight Configuration</flux:heading>

            <form wire:submit.prevent="saveFlightConfiguration" class="space-y-6">
                <!-- Departure Airport -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Departure Airport (ICAO)</label>
                    <select wire:model.live="departureAirport"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white">
                        <option value="">Select Departure Airport</option>
                        @foreach($this->airportOptions as $icao => $icaoDisplay)
                            <option value="{{ $icao }}">{{ $icaoDisplay }}</option>
                        @endforeach
                    </select>
                    @error('departureAirport')
                        <span class="mt-1 block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Arrival Airport -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Arrival Airport (ICAO)</label>
                    <select wire:model.live="arrivalAirport"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white">
                        <option value="">Select Arrival Airport</option>
                        @foreach($this->airportOptions as $icao => $icaoDisplay)
                            <option value="{{ $icao }}">{{ $icaoDisplay }}</option>
                        @endforeach
                    </select>
                    @error('arrivalAirport')
                        <span class="mt-1 block text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ETOPS -->
                <div class="flex items-center justify-between rounded-xl bg-gray-50 p-4 dark:bg-zinc-700">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ETOPS Flight</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Extended-range Twin-engine Operational Performance Standards</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" wire:model.live="etops" class="peer sr-only">
                        <div class="peer relative h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-blue-800"></div>
                    </label>
                </div>

                <!-- Live Animals -->
                <div class="flex items-center justify-between rounded-xl bg-gray-50 p-4 dark:bg-zinc-700">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Live Animals & Perishables</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Transporting live animals on this flight</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" wire:model.live="liveAnimals" class="peer sr-only">
                        <div class="peer relative h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-purple-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-purple-800"></div>
                    </label>
                </div>

                <!-- Hazmat -->
                <div class="flex items-center justify-between rounded-xl bg-gray-50 p-4 dark:bg-zinc-700">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">HAZMAT</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Transporting dangerous goods on this flight</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" wire:model.live="hazmat" class="peer sr-only">
                        <div class="peer relative h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-orange-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-orange-800"></div>
                    </label>
                </div>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        Save Configuration
                    </flux:button>

                    @if($departureAirport || $arrivalAirport)
                        <flux:button type="button" wire:click="clearFlight" variant="ghost">
                            Clear
                        </flux:button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Current Flight Details Card -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-800">
            <flux:heading size="lg" class="mb-4">Current Flight</flux:heading>

            @if($departureAirport || $arrivalAirport)
                <div class="space-y-4">
                    @if($departureAirport)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Departure:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $departureAirport }}</span>
                        </div>

                        <!-- METAR Display -->
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-zinc-600 dark:bg-zinc-700">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">METAR</span>
                                <div wire:loading wire:target="departureAirport">
                                    <svg class="h-4 w-4 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            @if($this->departureMetar)
                                <div class="space-y-2">
                                    @php
                                        $category = $this->departureMetar['flightCategory'];
                                        $colorClasses = match($category['color']) {
                                            'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-block rounded-full px-2 py-1 text-xs font-medium {{ $colorClasses }}">
                                        {{ $category['label'] }}
                                    </span>
                                    <p class="overflow-x-auto whitespace-pre-wrap break-all font-mono text-xs text-gray-700 dark:text-gray-300">{{ $this->departureMetar['raw'] }}</p>
                                </div>
                            @else
                                <p class="text-xs italic text-gray-500 dark:text-gray-400">No METAR data available</p>
                            @endif
                        </div>

                        <!-- Weather Conditions (2-column layout) -->
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Cold Weather Ops -->
                            <div class="flex items-center justify-between rounded-lg border-2 border-blue-400 bg-gray-50 p-3 dark:border-blue-500 dark:bg-zinc-700">
                                <div class="flex-1">
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Cold Weather</span>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" wire:model.live="coldWeatherOps" class="peer sr-only">
                                    <div class="peer relative h-5 w-9 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-cyan-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-cyan-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-cyan-800"></div>
                                </label>
                            </div>

                            <!-- LLWS -->
                            <div class="flex items-center justify-between rounded-lg border-2 border-purple-400 bg-gray-50 p-3 dark:border-purple-500 dark:bg-zinc-700">
                                <div class="flex-1">
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">LLWS</span>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" wire:model.live="llws" class="peer sr-only">
                                    <div class="peer relative h-5 w-9 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-red-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-red-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-red-800"></div>
                                </label>
                            </div>
                        </div>
                    @endif

                    @if($arrivalAirport)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-zinc-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Arrival:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $arrivalAirport }}</span>
                        </div>

                        <!-- TAF Display -->
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-zinc-600 dark:bg-zinc-700">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">TAF</span>
                                <div wire:loading wire:target="arrivalAirport">
                                    <svg class="h-4 w-4 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            @if($this->arrivalTaf)
                                <div class="max-h-40 overflow-y-auto">
                                    <p class="overflow-x-auto whitespace-pre-wrap break-all font-mono text-xs text-gray-700 dark:text-gray-300">{{ $this->arrivalTaf['raw'] }}</p>
                                </div>
                            @else
                                <p class="text-xs italic text-gray-500 dark:text-gray-400">No TAF data available</p>
                            @endif
                        </div>
                    @endif

                    @if($etops)
                        <div class="flex items-center justify-between rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <span class="text-sm text-gray-600 dark:text-gray-400">ETOPS:</span>
                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-800 dark:bg-blue-900 dark:text-blue-200">Enabled</span>
                        </div>
                    @endif

                    @if($liveAnimals)
                        <div class="flex items-center justify-between rounded-lg bg-purple-50 p-4 dark:bg-purple-900/20">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Live Animals:</span>
                            <span class="rounded-full bg-purple-100 px-2 py-1 text-xs text-purple-800 dark:bg-purple-900 dark:text-purple-200">Yes</span>
                        </div>
                    @endif

                    @if($hazmat)
                        <div class="flex items-center justify-between rounded-lg bg-orange-50 p-4 dark:bg-orange-900/20">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Hazmat:</span>
                            <span class="rounded-full bg-orange-100 px-2 py-1 text-xs text-orange-800 dark:bg-orange-900 dark:text-orange-200">Yes</span>
                        </div>
                    @endif

                    @if($coldWeatherOps)
                        <div class="flex items-center justify-between rounded-lg bg-cyan-50 p-4 dark:bg-cyan-900/20">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Cold Weather Ops:</span>
                            <span class="rounded-full bg-cyan-100 px-2 py-1 text-xs text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">Active</span>
                        </div>
                    @endif

                    @if($llws)
                        <div class="flex items-center justify-between rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                            <span class="text-sm text-gray-600 dark:text-gray-400">LLWS:</span>
                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-800 dark:bg-red-900 dark:text-red-200">Reported</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="flex h-80 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-zinc-600">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No flight configured</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
