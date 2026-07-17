<div
    wire:partial="pg-enabled-filters-{{ $__partial->tableName }}"
    @class(['pg-enabled-filters-base flex flex-wrap gap-2' => count($enabledFilters)])
    @if (count($enabledFilters)) data-cy="enabled-filters" @endif
>
    @if (count($enabledFilters))
        @if (count($enabledFilters) > 1)
            <div class="flex group items-center cursor-pointer">
                <span
                    wire:click.prevent="clearAllFilters"
                    class="badge badge-neutral gap-1 hover:bg-base-300 transition-colors"
                >
                    {{ trans('livewire-powergrid::datatable.buttons.clear_all_filters') }}
                    <x-livewire-powergrid::icons.x class="w-3.5 h-3.5" />
                </span>
            </div>
        @endif

        @foreach ($enabledFilters as $filter)
            @isset($filter['label'])
                <div
                    wire:key="enabled-filters-{{ $filter['field'] }}"
                    class="flex group items-center cursor-pointer"
                >
                    <span
                        data-cy="enabled-filters-clear-{{ $filter['field'] }}"
                        wire:click.prevent="clearFilter('{{ $filter['field'] }}')"
                        class="badge badge-outline gap-1 hover:bg-base-200 transition-colors"
                    >
                        {{ $filter['label'] }}
                        <x-livewire-powergrid::icons.x class="w-3.5 h-3.5" />
                    </span>
                </div>
            @endisset
        @endforeach
    @endif
</div>
