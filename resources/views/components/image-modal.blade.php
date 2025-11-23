@props(['images', 'modalId'])

@if(isset($images) && is_array($images) && count($images) > 0)
    <!-- Image Icon Trigger Button -->
    <button @click.stop="$flux.modal('{{ $modalId }}').show()"
            class="flex-shrink-0 p-1.5 text-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
    </button>

    <!-- Flux Modal -->
    <flux:modal name="{{ $modalId }}" class="w-full md:w-3/4">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">Reference Images</flux:heading>
            </div>

            <flux:separator />

            <div class="space-y-4">
                @foreach($images as $index => $image)
                    <div class="space-y-2">
                        @if(isset($image['title']))
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $image['title'] }}</h4>
                        @endif

                        @if(isset($image['url']))
                            <div class="bg-gray-100 dark:bg-zinc-800 rounded-lg overflow-hidden">
                                <img src="{{ $image['url'] }}"
                                     alt="{{ $image['alt'] ?? 'Reference image ' . ($index + 1) }}"
                                     class="w-full h-auto object-contain">
                            </div>
                        @endif

                        @if(isset($image['caption']))
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $image['caption'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <flux:modal.close>
                    <flux:button variant="primary">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
@endif
