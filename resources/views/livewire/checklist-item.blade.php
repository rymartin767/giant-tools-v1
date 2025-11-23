<div @class([
    'ml-8' => $isSubItem
])>
    <!-- Checklist Item -->
    <div @class([
             'flex items-center p-4 rounded-2xl transition-all duration-200 group touch-manipulation',
             'bg-green-50 border-l-4 border-green-400 dark:bg-green-900/20 dark:border-green-500' => $isCompleted,
             'bg-white border border-gray-200 hover:border-gray-300 dark:bg-zinc-800 dark:border-zinc-700 dark:hover:border-zinc-600' => !$isCompleted
         ])>

        <!-- Checkbox -->
        <div wire:click="toggle"
             @click="if ('vibrate' in navigator) { navigator.vibrate(50); }"
             class="flex-shrink-0 mr-4 cursor-pointer">
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
            <!-- Title Row: text + modal button inline -->
            <div class="flex items-center gap-2">
                <div wire:click="toggle"
                     @click="if ('vibrate' in navigator) { navigator.vibrate(50); }"
                     class="cursor-pointer flex-1">
                    <span @class([
                        'text-sm font-medium transition-all duration-200',
                        'line-through text-gray-500 dark:text-gray-400' => $isCompleted,
                        'text-gray-800 dark:text-gray-200' => !$isCompleted
                    ])>
                        {{ $itemText }}
                        @if ($this->hasNotes() && $this->getNotesType() === 'text')
                            <span class="text-xs text-blue-600 dark:text-blue-400 ml-2">
                                ({{ $notes['content'] }})
                            </span>
                        @endif
                    </span>
                </div>

                <!-- Modal Notes Button (inline with title) -->
                @if ($this->hasNotes() && $this->getNotesType() === 'modal')
                    <button @click.stop="$flux.modal('{{ $this->getModalId() }}').show()"
                            class="flex-shrink-0 p-1.5 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                @endif

                <!-- Image Modal Button (inline with title) -->
                @if ($this->hasImages())
                    <x-image-modal :images="$images" :modalId="$this->getImageModalId()" />
                @endif
            </div>
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

    <!-- Flux Modal -->
    @if ($this->hasNotes() && $this->getNotesType() === 'modal')
        <flux:modal name="{{ $this->getModalId() }}" class="md:w-96">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ $notes['title'] ?? 'Information' }}</flux:heading>
                </div>

                <flux:separator />

                <div class="prose dark:prose-invert max-w-none">
                    <p class="text-gray-700 dark:text-gray-300">{{ $notes['content'] }}</p>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:modal.close>
                        <flux:button variant="primary">Close</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>
    @endif

    <!-- Subitems -->
    @if ($this->hasSubitems())
        <div class="mt-2 space-y-2">
            @foreach ($subitems as $subIndex => $subitem)
                @livewire('checklist-item', [
                    'stepKey' => $stepKey,
                    'itemIndex' => $itemIndex . '-' . $subIndex,
                    'item' => $subitem,
                    'isCompleted' => in_array($itemIndex . '-' . $subIndex, $completedSubitems),
                    'isSubItem' => true
                ], key("subitem-{$stepKey}-{$itemIndex}-{$subIndex}"))
            @endforeach
        </div>
    @endif
</div>
