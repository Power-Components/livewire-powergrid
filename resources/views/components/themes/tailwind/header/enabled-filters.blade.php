
@if (count($enabledFilters))
    <div
        data-cy="enabled-filters"
        class="pg-enabled-filters-base"
    >
        @if (count($enabledFilters) > 1)
            <div class="flex group items-center gap-3 cursor-pointer">
                <span
                    wire:click.prevent="clearAllFilters"
                    class="select-none rounded-md outline-none inline-flex items-center border px-2 py-0.5 font-bold text-xs border-pg-primary-500 bg-pg-primary-100 dark:border-pg-primary-500 dark:bg-pg-primary-900 dark:text-pg-primary-300 dark:hover:text-pg-primary-400 text-pg-primary-600 hover:text-pg-primary-500"
                >
                    {{ trans('livewire-powergrid::datatable.buttons.clear_all_filters') }}
                    <x-livewire-powergrid::icons.x class="w-4 h-4 ml-1" />
                </span>
            </div>
        @endif

        @foreach ($enabledFilters as $filter)
            @isset($filter['label'])
                <div
                    wire:key="enabled-filters-{{ $filter['field'] }}"
                    class="flex group items-center gap-3 cursor-pointer"
                >
                    <span
                        data-cy="enabled-filters-clear-{{ $filter['field'] }}"
                        wire:click.prevent="clearFilter('{{ $filter['field'] }}')"
                        class="select-none rounded-md outline-none inline-flex items-center border px-2 py-0.5 font-bold text-xs border-pg-primary-300 bg-white dark:border-pg-primary-600 dark:bg-pg-primary-800 dark:text-pg-primary-300 dark:hover:text-pg-primary-400 text-pg-primary-600 hover:text-pg-primary-500"
                    >
                        {{ $filter['label'] }}
                        <x-livewire-powergrid::icons.x class="w-4 h-4 ml-1" />
                    </span>
                </div>
            @endisset
        @endforeach
    </div>
@endif
