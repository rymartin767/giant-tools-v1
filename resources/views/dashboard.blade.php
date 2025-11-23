<x-layouts.pwa :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @php
            $view = request()->get('view', 'checklist');
        @endphp

        @if($view === 'trip')
            <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                @livewire('trip')
            </div>
        @elseif($view === 'flight')
            <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                @livewire('flight')
            </div>
        @elseif($view === 'aircraft')
            <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                @livewire('aircraft')
            </div>
        @elseif($view === 'briefing')
            <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                @livewire('briefing')
            </div>
        @else
            <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                @livewire('checklist')
            </div>
        @endif
    </div>
</x-layouts.pwa>
