<div
    wire:partial="pg-enabled-filters-{{ $__partial->tableName }}"
    @class(['pg-enabled-filters-base flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-y border-base-300' => count($enabledFilters)])
    @if (count($enabledFilters)) data-cy="enabled-filters" @endif
>
    @if (count($enabledFilters))
        @php($element = $__partial->headerElement('clearFilters'))

        <div class="flex flex-wrap items-center gap-2">
            <span class="{{ theme('header.enabled_filters.label', 'text-xs font-medium text-base-content/60') }}">
                {{ trans('livewire-powergrid::datatable.labels.active_filters') }}
            </span>

            @foreach ($enabledFilters as $filter)
                @isset($filter['label'])
                    <span
                        role="button"
                        data-cy="enabled-filters-clear-{{ $filter['field'] }}"
                        wire:key="enabled-filters-{{ $filter['field'] }}"
                        wire:click.prevent="clearFilter('{{ $filter['field'] }}')"
                        class="{{ theme('header.enabled_filters.pill', 'badge badge-outline badge-primary gap-1 hover:bg-base-200 transition-colors') }}"
                    >
                        {{ $filter['label'] }}
                        {!! $element['iconHtml'] !!}
                    </span>
                @endisset
            @endforeach
        </div>

        <button
            type="button"
            role="button"
            data-cy="enabled-filters-clear-all"
            wire:click.prevent="clearAllFilters"
            title="{{ $element['title'] }}"
            aria-label="{{ $element['title'] }}"
            class="{{ theme('header.enabled_filters.pill_clear_all', 'badge badge-neutral gap-1 hover:bg-base-300 transition-colors') }}"
        >
            {!! $element['iconHtml'] !!}
        </button>
    @endif
</div>
