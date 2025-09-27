<div class="h-full bg-gray-50 dark:bg-zinc-900 overflow-hidden"
     x-data="checklistMain()"
     @item-toggled.window="handleItemToggled($event.detail)"
     @keydown.escape="showSettings = false">

    <!-- Settings Modal -->
    <div x-show="showSettings"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50"
         style="display: none;">

        <div class="flex min-h-full items-center justify-center p-4">
            <div @click.away="showSettings = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="w-full max-w-md bg-white dark:bg-zinc-800 rounded-2xl shadow-xl p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Flight Settings</h3>
                    <button @click="showSettings = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Flight Details Display -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Engine Type:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $engines }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Configuration:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $config }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Departure:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $departureAirport }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Arrival:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $arrivalAirport }}</span>
                    </div>
                    @if($supernumeraries)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Supernumeraries:</span>
                        <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full">Yes</span>
                    </div>
                    @endif
                </div>

                <!-- Airport Notes -->
                @if($this->getAirportNotes($departureAirport))
                    @php $departureNotes = $this->getAirportNotes($departureAirport); @endphp
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-2">{{ $departureAirport }} Notes</h4>
                        <div class="space-y-2 text-xs text-yellow-700 dark:text-yellow-300">
                            @if(isset($departureNotes['runway']))
                                <div><strong>Runway:</strong> {{ $departureNotes['runway'] }}</div>
                            @endif
                            @if(isset($departureNotes['typical_delays']))
                                <div><strong>Delays:</strong> {{ $departureNotes['typical_delays'] }}</div>
                            @endif
                            @if(isset($departureNotes['terminal_specific_procedures']))
                                <div><strong>Procedures:</strong> {{ $departureNotes['terminal_specific_procedures'] }}</div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <button wire:click="resetFlightDetails"
                            class="w-full px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl transition-colors duration-200 font-medium">
                        Change Flight Details
                    </button>
                    <button wire:click="resetChecklist"
                            class="w-full px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl transition-colors duration-200 font-medium">
                        Reset Checklist
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="h-full flex flex-col">
        <!-- Progress Bar -->
        <div class="bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                <button @click="showSettings = true"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>
            <div class="bg-gray-200 dark:bg-zinc-700 rounded-full h-3 overflow-hidden">
                <div class="bg-blue-500 h-full rounded-full transition-all duration-500 ease-out"
                     style="width: {{ $this->getProgressPercentage() }}%"></div>
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                {{ round($this->getProgressPercentage()) }}% Complete
            </div>
        </div>

        <!-- Step Navigation -->
        <div class="bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700 p-4 overflow-x-auto">
            <div class="flex space-x-3 min-w-max">
                @foreach ($stepKeys as $stepKey)
                    <button wire:click="goToStep('{{ $stepKey }}')"
                            @class([
                                'flex-shrink-0 flex flex-col items-center p-3 rounded-xl transition-all duration-200',
                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $this->isStepComplete($stepKey),
                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 ring-2 ring-blue-300 dark:ring-blue-700' => $stepKey == $currentStep,
                                'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-600' => $stepKey != $currentStep && !$this->isStepComplete($stepKey)
                            ])>
                        <div @class([
                            'w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold',
                            'bg-green-500 text-white' => $this->isStepComplete($stepKey),
                            'bg-blue-500 text-white' => $stepKey == $currentStep,
                            'bg-gray-300 text-gray-600 dark:bg-zinc-600 dark:text-gray-300' => $stepKey != $currentStep && !$this->isStepComplete($stepKey)
                        ])>
                            @if ($this->isStepComplete($stepKey))
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif ($stepKey == $currentStep)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Current Step Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-4">
                <!-- Step Header -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 text-white mb-6">
                    <h2 class="text-xl font-bold mb-2">{{ $steps[$currentStep]['title'] }}</h2>
                    <p class="text-blue-100 text-sm mb-4">{{ $steps[$currentStep]['description'] }}</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="bg-white/20 px-3 py-1 rounded-full">Step {{ $currentStep }}</span>
                        <span>{{ count($this->completedItems[$currentStep] ?? []) }}/{{ count($this->getFilteredItems($currentStep)) }} complete</span>
                    </div>
                </div>

                <!-- Checklist Items -->
                <div class="space-y-3 mb-6">
                    @php $filteredItems = $this->getFilteredItems($currentStep); @endphp

                    @foreach ($filteredItems as $index => $item)
                        @php $isCompleted = in_array($index, $this->completedItems[$currentStep] ?? []); @endphp

                        <div wire:click="toggleItem('{{ $currentStep }}', {{ $index }})"
                             @click="triggerHapticFeedback()"
                             @class([
                                 'flex items-start p-4 rounded-2xl cursor-pointer transition-all duration-200 group touch-manipulation',
                                 'bg-green-50 border-l-4 border-green-400 dark:bg-green-900/20 dark:border-green-500' => $isCompleted,
                                 'bg-white border border-gray-200 hover:border-gray-300 dark:bg-zinc-800 dark:border-zinc-700 dark:hover:border-zinc-600' => !$isCompleted
                             ])>

                            <!-- Checkbox -->
                            <div class="flex-shrink-0 mr-4 mt-1">
                                <div @class([
                                    'w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-200',
                                    'bg-green-500 border-green-500' => $isCompleted,
                                    'border-gray-300 dark:border-zinc-600 group-hover:border-blue-400' => !$isCompleted
                                ])>
                                    @if ($isCompleted)
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Task Content -->
                            <div class="flex-1 min-w-0">
                                <span @class([
                                    'text-sm font-medium transition-all duration-200 block',
                                    'line-through text-gray-500 dark:text-gray-400' => $isCompleted,
                                    'text-gray-800 dark:text-gray-200' => !$isCompleted
                                ])>
                                    {{ $item }}
                                </span>
                            </div>

                            <!-- Completion Badge -->
                            @if ($isCompleted)
                                <div class="flex-shrink-0 ml-3">
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium dark:bg-green-900 dark:text-green-200">
                                        ✓
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Navigation -->
                <div class="flex justify-between items-center gap-4 pb-8">
                    @php
                        $currentIndex = array_search($currentStep, $stepKeys);
                        $hasPrevious = $currentIndex !== false && $currentIndex > 0;
                        $hasNext = $currentIndex !== false && $currentIndex < count($stepKeys) - 1;
                    @endphp

                    @if ($hasPrevious)
                        <button wire:click="previousStep"
                                class="flex-1 flex items-center justify-center px-6 py-4 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 font-medium transition-colors duration-200 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Previous
                        </button>
                    @else
                        <div class="flex-1"></div>
                    @endif

                    @if ($hasNext)
                        <button wire:click="nextStep"
                                @click="triggerHapticFeedback()"
                                @class([
                                    'flex-1 flex items-center justify-center px-6 py-4 rounded-xl font-semibold transition-all duration-200',
                                    'bg-blue-500 hover:bg-blue-600 text-white shadow-lg hover:shadow-xl transform hover:-translate-y-0.5' => $this->isStepComplete($currentStep),
                                    'bg-gray-300 text-gray-500 cursor-not-allowed dark:bg-zinc-700 dark:text-zinc-500' => !$this->isStepComplete($currentStep)
                                ])
                                @if (!$this->isStepComplete($currentStep)) disabled @endif>
                            Next Step
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @else
                        @if ($this->isStepComplete($currentStep))
                            <div class="flex-1 flex items-center justify-center text-green-600 dark:text-green-400 font-semibold bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Flight Ready!
                            </div>
                        @else
                            <div class="flex-1 text-center text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-zinc-800 rounded-xl p-4">
                                Complete all items
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Completion Celebration -->
                @if (!$hasNext && $this->isStepComplete($currentStep))
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-6 text-center">
                        <div class="text-green-800 dark:text-green-200">
                            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">All Checks Complete!</h3>
                            <p class="text-sm">Your aircraft is ready for departure. Safe travels!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function checklistMain() {
    return {
        showSettings: false,

        handleItemToggled(data) {
            this.triggerHapticFeedback();

            // Save to offline storage
            if (window.ChecklistOffline) {
                window.ChecklistOffline.saveChecklistData({
                    userId: @js(auth()->id()),
                    stepKey: data.stepKey,
                    itemIndex: data.itemIndex,
                    completed: data.completed,
                    timestamp: Date.now()
                });
            }
        },

        triggerHapticFeedback() {
            // Trigger haptic feedback on supported devices
            if ('vibrate' in navigator) {
                navigator.vibrate(50);
            }

            // iOS haptic feedback (if available)
            if (window.DeviceMotionEvent && typeof DeviceMotionEvent.requestPermission === 'function') {
                // iOS haptic feedback would go here
            }
        }
    }
}
</script>