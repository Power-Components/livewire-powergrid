@if (data_get($setUp, 'header.toggleColumns'))
    <div
        x-data="{ open: false }"
        class="mr-2 mt-2 sm:mt-0"
        @click.outside="open = false"
    >
        <button
            @click.prevent="open = ! open"
            class="pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700
    dark:ring-offset-pg-primary-800 dark:text-pg-primary-400 dark:bg-pg-primary-700"
        >
            <div class="flex">
                <x-livewire-powergrid::icons.eye-off class="w-5 h-5 text-pg-primary-500 dark:text-pg-primary-300" />
            </div>
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition:enter="transform duration-200"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transform duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="mt-2 py-2 w-48 bg-white shadow-xl absolute z-10 dark:bg-pg-primary-700"
        >
            @foreach ($this->visibleColumns as $column)
                <div
                        wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ $column->field }}'})"
                        wire:key="toggle-column-{{ $column->field }}"
                        class="@if ($column->hidden) opacity-40 bg-pg-primary-300 dark:bg-pg-primary-800 @endif cursor-pointer flex justify-start block px-4 py-2 text-pg-primary-800 hover:bg-pg-primary-50 hover:text-black-200 dark:text-pg-primary-200 dark:hover:bg-gray-900 dark:hover:bg-pg-primary-700"
                >
                    @if (!$column->hidden)
                        <x-livewire-powergrid::icons.eye class="text-pg-primary-500 dark:text-pg-primary-300" />
                    @else
                        <x-livewire-powergrid::icons.eye-off class="text-pg-primary-500 dark:text-pg-primary-300" />
                    @endif
                    <div class="ml-2">
                        {!! $column->title !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
