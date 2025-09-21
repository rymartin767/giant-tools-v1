<div class="h-full bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-zinc-900 dark:to-zinc-800 p-4"
     x-data="checklistFlightDetails()"
     @offline-sync.window="handleOfflineSync($event.detail)">

    <div class="max-w-md mx-auto h-full flex flex-col justify-center">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Flight Configuration</h1>
            <p class="text-gray-600 dark:text-gray-300">Configure your flight details before starting the checklist</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-xl p-6 space-y-6">
            <form wire:submit.prevent="completeFlightDetails" class="space-y-6">
                <!-- Engines Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Engine Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($engineOptions as $option)
                            <label class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 {{ $engines === $option ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-zinc-600 hover:border-gray-300 dark:hover:border-zinc-500' }}">
                                <input type="radio" wire:model.live="engines" value="{{ $option }}" class="sr-only">
                                <span class="text-lg font-semibold {{ $engines === $option ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $option }}</span>
                                @if($engines === $option)
                                    <svg class="absolute top-2 right-2 w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @error('engines') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Configuration Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Configuration</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($configOptions as $option)
                            <label class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 {{ $config === $option ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-zinc-600 hover:border-gray-300 dark:hover:border-zinc-500' }}">
                                <input type="radio" wire:model.live="config" value="{{ $option }}" class="sr-only">
                                <span class="text-lg font-semibold {{ $config === $option ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $option }}</span>
                                @if($config === $option)
                                    <svg class="absolute top-2 right-2 w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @error('config') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Airport Selections -->
                <div class="grid grid-cols-1 gap-6">
                    <!-- Departure Airport -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departure Airport</label>
                        <select wire:model.live="departureAirport"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:text-white text-lg">
                            <option value="">Select Departure Airport</option>
                            @foreach($airportOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('departureAirport') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Arrival Airport -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Arrival Airport</label>
                        <select wire:model.live="arrivalAirport"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-zinc-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:text-white text-lg">
                            <option value="">Select Arrival Airport</option>
                            @foreach($airportOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('arrivalAirport') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Supernumeraries -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700 rounded-xl">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Supernumeraries</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Are there additional crew members?</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="supernumeraries" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        :disabled="!isFormValid"
                        class="w-full bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:transform-none disabled:shadow-none">
                    <span wire:loading.remove>Start Checklist</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Starting...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function checklistFlightDetails() {
    return {
        get isFormValid() {
            return this.$wire.engines && this.$wire.config && this.$wire.departureAirport && this.$wire.arrivalAirport;
        },

        handleOfflineSync(data) {
            // Store flight details offline
            if (window.ChecklistOffline) {
                window.ChecklistOffline.saveFlightDetails(data);
            }
        }
    }
}
</script>