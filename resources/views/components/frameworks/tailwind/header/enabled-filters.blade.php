@if (count($enabledFilters))
    <div class="flex items-center gap-2 mt-2 md:mt-0 flex-wrap">
        @if (count($enabledFilters) > 1)
            <span
                class="outline-none inline-flex justify-center items-center group rounded gap-x-1 text-xs font-semibold px-2.5 py-0.5 text-pg-primary-100 bg-pg-primary-600 dark:bg-pg-primary-800"
            >
                {{ trans('livewire-powergrid::datatable.buttons.clear_all_filters') }}
                <div class="relative flex items-center w-2 h-2">
                    <button
                        wire:click.prevent="clearAllFilters"
                        type="button"
                    >
                        <x-livewire-powergrid::icons.x class="w-4 h-4" />
                    </button>
                </div>
            </span>
        @endif
        @foreach ($enabledFilters as $field => $filter)
            @isset($filter['label'])
                <span
                    wire:key="enabled-filters-{{ $field }}"
                    class="outline-none inline-flex justify-center items-center group rounded gap-x-1 text-xs font-semibold px-2.5 py-0.5 text-pg-primary-600 dark:text-pg-primary-200 bg-pg-primary-100 dark:bg-pg-primary-700"
                >
                    {{ $filter['label'] }}
                    <div class="relative flex items-center w-2 h-2">
                        <button
                            wire:click.prevent="clearFilter('{{ $field }}')"
                            type="button"
                        >
                            <x-livewire-powergrid::icons.x class="w-4 h-4" />
                        </button>
                    </div>
                </span>
            @endisset
        @endforeach
    </div>
@endif
