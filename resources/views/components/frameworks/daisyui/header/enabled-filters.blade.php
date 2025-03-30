
@if (count($enabledFilters))
    <div
        data-cy="enabled-filters"
        class="pg-enabled-filters-base mb-3 flex gap-2 items-center"
    >
        @if (count($enabledFilters) > 1)
            <div class="flex group items-center cursor-pointer">
                <span
                    wire:click.prevent="clearAllFilters"
                    class="cursor-pointer badge badge-neutral badge-sm"
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
                    class="flex group items-center cursor-pointer"
                >
                    <button
                        data-cy="enabled-filters-clear-{{ $filter['field'] }}"
                        wire:click.prevent="clearFilter('{{ $filter['field'] }}')"
                        class="cursor-pointer badge badge-primary badge-sm"
                    >
                        {{ $filter['label'] }}
                        <x-livewire-powergrid::icons.x class="w-4 h-4" />
                    </button>
                </div>
            @endisset
        @endforeach
    </div>
@endif
