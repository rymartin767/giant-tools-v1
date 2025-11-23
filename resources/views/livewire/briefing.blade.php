<div class="h-full bg-gray-50 dark:bg-zinc-900 overflow-hidden"
     x-data="briefingMain()"
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Briefing Settings</h3>
                    <button @click="showSettings = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Flight Configuration Display -->
                <div class="space-y-3">
                    @if($this->engines)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Engine Type:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $this->engines }}</span>
                    </div>
                    @endif

                    @if($this->config)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Configuration:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $this->config }}</span>
                    </div>
                    @endif

                    @if($this->supernumeraries)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Supernumeraries:</span>
                        <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full">Yes</span>
                    </div>
                    @endif

                    @if($this->etops)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">ETOPS:</span>
                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">Yes</span>
                    </div>
                    @endif
                </div>

                <!-- Action Button -->
                <div class="pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <button wire:click="resetBriefing"
                            class="w-full px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl transition-colors duration-200 font-medium">
                        Reset Briefing
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="h-full flex flex-col">
        <!-- Progress Bar -->
        <div class="bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                <button @click="showSettings = true"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>
            <div class="bg-gray-200 dark:bg-zinc-700 rounded-full h-2 sm:h-3 overflow-hidden">
                <div class="bg-indigo-500 h-full rounded-full transition-all duration-500 ease-out"
                     style="width: {{ $this->getProgressPercentage() }}%"></div>
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                {{ round($this->getProgressPercentage()) }}% Complete
            </div>
        </div>

        <!-- Step Navigation -->
        <div class="bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700 p-3 sm:p-4">
            <div class="flex flex-wrap gap-2 justify-center">
                @foreach ($stepKeys as $stepKey)
                    <button wire:click="goToStep('{{ $stepKey }}')"
                            @class([
                                'flex items-center justify-center rounded-full transition-all duration-200 w-10 h-10 sm:w-12 sm:h-12',
                                'bg-green-500 text-white shadow-sm' => $this->isStepComplete($stepKey),
                                'bg-indigo-500 text-white ring-2 ring-indigo-300 dark:ring-indigo-700 shadow-md' => $stepKey == $currentStep,
                                'bg-gray-300 text-gray-600 dark:bg-zinc-600 dark:text-gray-300 hover:bg-gray-400 dark:hover:bg-zinc-500' => $stepKey != $currentStep && !$this->isStepComplete($stepKey)
                            ])>
                        @if ($this->isStepComplete($stepKey))
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <span class="text-sm sm:text-base font-semibold">{{ $stepKey }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Current Step Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-3 sm:p-4">
                <!-- Step Header -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-bold mb-2">{{ $steps[$currentStep]['title'] }}</h2>
                    <p class="text-indigo-100 text-xs sm:text-sm mb-3 sm:mb-4">{{ $steps[$currentStep]['description'] }}</p>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:justify-between text-xs sm:text-sm">
                        <span class="bg-white/20 px-3 py-1 rounded-full whitespace-nowrap">Step {{ $currentStep }}</span>
                        <span class="text-indigo-100">{{ count($this->completedItems[$currentStep] ?? []) }}/{{ count($this->getFilteredItems($currentStep)) }} complete</span>
                    </div>
                </div>

                <!-- Briefing Items -->
                <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                    @foreach ($this->getFilteredItems($currentStep) as $index => $item)
                        <div @class([
                            'pl-4 sm:pl-6' => $item['isSubItem'] ?? false
                        ])>
                            @livewire('checklist-item', [
                                'stepKey' => $currentStep,
                                'itemIndex' => $index,
                                'item' => $item,
                                'isCompleted' => in_array((string)$index, array_map('strval', $this->completedItems[$currentStep] ?? []), true),
                                'isSubItem' => $item['isSubItem'] ?? false
                            ], key("briefing-item-{$currentStep}-{$index}"))
                        </div>
                    @endforeach
                </div>

                <!-- Navigation -->
                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 sm:gap-4 pb-6 sm:pb-8">
                    @php
                        $currentIndex = array_search($currentStep, $stepKeys);
                        $hasPrevious = $currentIndex !== false && $currentIndex > 0;
                        $hasNext = $currentIndex !== false && $currentIndex < count($stepKeys) - 1;
                    @endphp

                    @if ($hasPrevious)
                        <button wire:click="previousStep"
                                class="flex items-center justify-center px-4 sm:px-6 py-3 sm:py-4 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 font-medium transition-colors duration-200 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 w-full sm:flex-1">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span class="text-sm sm:text-base">Previous</span>
                        </button>
                    @endif

                    @if ($hasNext)
                        <button wire:click="nextStep"
                                @click="triggerHapticFeedback()"
                                @class([
                                    'flex items-center justify-center px-4 sm:px-6 py-3 sm:py-4 rounded-xl font-semibold transition-all duration-200 w-full sm:flex-1',
                                    'bg-indigo-500 hover:bg-indigo-600 text-white shadow-lg hover:shadow-xl' => $this->isStepComplete($currentStep),
                                    'bg-gray-300 text-gray-500 cursor-not-allowed dark:bg-zinc-700 dark:text-zinc-500' => !$this->isStepComplete($currentStep)
                                ])
                                @if (!$this->isStepComplete($currentStep)) disabled @endif>
                            <span class="text-sm sm:text-base">Next Step</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @else
                        @if ($this->isStepComplete($currentStep))
                            <div class="flex items-center justify-center text-green-600 dark:text-green-400 font-semibold bg-green-50 dark:bg-green-900/20 rounded-xl p-3 sm:p-4 w-full sm:flex-1">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm sm:text-base">Briefing Complete!</span>
                            </div>
                        @else
                            <div class="text-center text-gray-500 dark:text-gray-400 font-medium bg-gray-50 dark:bg-zinc-800 rounded-xl p-3 sm:p-4 w-full sm:flex-1">
                                <span class="text-sm sm:text-base">Complete all items</span>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Completion Celebration -->
                @if (!$hasNext && $this->isStepComplete($currentStep))
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-center">
                        <div class="text-green-800 dark:text-green-200">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold mb-2">Briefing Complete!</h3>
                            <p class="text-xs sm:text-sm">All briefing items have been reviewed. Ready to proceed!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function briefingMain() {
    return {
        showSettings: false,

        handleItemToggled(data) {
            this.triggerHapticFeedback();

            // Save to offline storage
            if (window.BriefingOffline) {
                window.BriefingOffline.saveBriefingData({
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
