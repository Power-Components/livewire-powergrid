
@if (count($enabledFilters))
    <div
        data-cy="enabled-filters"
        class="pg-enabled-filters-base"
    >
        @if (count($enabledFilters) > 1)
            <div class="flex group items-center gap-3 cursor-pointer">
                <span
                    wire:click.prevent="clearAllFilters"
                    class="select-none rounded-md outline-none inline-flex items-center border px-2 py-0.5 font-bold text-xs border-zinc-500 bg-zinc-100 dark:border-zinc-500 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-400 text-zinc-600 hover:text-zinc-500"
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
                        class="select-none rounded-md outline-none inline-flex items-center border px-2 py-0.5 font-bold text-xs border-zinc-300 bg-white dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:text-zinc-400 text-zinc-600 hover:text-zinc-500"
                    >
                        {{ $filter['label'] }}
                        <x-livewire-powergrid::icons.x class="w-4 h-4 ml-1" />
                    </span>
                </div>
            @endisset
        @endforeach
    </div>
@endif
