<div
    wire:partial="pg-enabled-filters-{{ $__partial->tableName }}"
    @class(['pg-enabled-filters-base flex flex-wrap gap-2' => count($enabledFilters)])
    @if (count($enabledFilters)) data-cy="enabled-filters" @endif
>
    @if (count($enabledFilters))
        @php($element = $__partial->headerElement('clearFilters'))
        @if (count($enabledFilters) > 1)
            <div class="{{ theme('header.enabled_filters.wrapper', 'flex group items-center cursor-pointer') }}">
                <span
                    wire:click.prevent="clearAllFilters"
                    title="{{ $element['title'] }}"
                    class="{{ theme('header.enabled_filters.pill_clear_all', 'badge badge-neutral gap-1 hover:bg-base-300 transition-colors') }}"
                >
                    @if ($element['showLabel'])
                        {{ $element['title'] }}
                    @endif
                    {!! $element['iconHtml'] !!}
                </span>
            </div>
        @endif

        @foreach ($enabledFilters as $filter)
            @isset($filter['label'])
                <div
                    wire:key="enabled-filters-{{ $filter['field'] }}"
                    class="{{ theme('header.enabled_filters.wrapper', 'flex group items-center cursor-pointer') }}"
                >
                    <span
                        data-cy="enabled-filters-clear-{{ $filter['field'] }}"
                        wire:click.prevent="clearFilter('{{ $filter['field'] }}')"
                        class="{{ theme('header.enabled_filters.pill', 'badge badge-outline gap-1 hover:bg-base-200 transition-colors') }}"
                    >
                        {{ $filter['label'] }}
                        {!! $element['iconHtml'] !!}
                    </span>
                </div>
            @endisset
        @endforeach
    @endif
</div>
