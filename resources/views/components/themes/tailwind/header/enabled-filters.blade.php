<div
    wire:partial="pg-enabled-filters-{{ $__partial->tableName }}"
    @class(['pg-enabled-filters-base flex flex-wrap items-center gap-2 my-3 md:mt-0' => count($enabledFilters)])
    @if (count($enabledFilters)) data-cy="enabled-filters" @endif
>
    @if (count($enabledFilters))
        @php
            $element = $__partial->headerElement('clearFilters');
        @endphp
        @if (count($enabledFilters) > 1)
            <div class="{{ theme('header.enabled_filters.wrapper') }}">
                <span
                    wire:click.prevent="clearAllFilters"
                    title="{{ $element['title'] }}"
                    class="{{ theme('header.enabled_filters.pill_clear_all') }}"
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
                @php
                    $isBuilderPill = ($filter['source'] ?? null) === 'filterBuilder';
                    $pillClick = $isBuilderPill
                        ? "clearFilterBuilderRow(".intval($filter['index'] ?? 0).")"
                        : "clearFilter('".$filter['field']."')";
                @endphp
                <div
                    wire:key="enabled-filters-{{ $isBuilderPill ? 'fb-'.($filter['index'] ?? 0) : $filter['field'] }}"
                    class="{{ theme('header.enabled_filters.wrapper') }}"
                >
                    <span
                        data-cy="enabled-filters-clear-{{ $isBuilderPill ? 'fb-'.($filter['index'] ?? 0) : $filter['field'] }}"
                        wire:click.prevent="{{ $pillClick }}"
                        class="{{ theme('header.enabled_filters.pill') }}"
                    >
                        {{ $filter['label'] }}
                        {!! $element['iconHtml'] !!}
                    </span>
                </div>
            @endisset
        @endforeach
    @endif
</div>
