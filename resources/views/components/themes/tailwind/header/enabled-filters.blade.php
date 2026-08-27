<div
    wire:partial="pg-enabled-filters-{{ $__partial->tableName }}"
    @class(['pg-enabled-filters-base flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-y border-zinc-200 dark:border-zinc-700' => count($enabledFilters)])
    @if (count($enabledFilters)) data-cy="enabled-filters" @endif
>
    @if (count($enabledFilters))
        @php
            $element = $__partial->headerElement('clearFilters');
        @endphp

        <div class="flex flex-wrap items-center gap-2">
            <span class="{{ theme('header.enabled_filters.label') }}">
                {{ trans('livewire-powergrid::datatable.labels.active_filters') }}
            </span>

            @foreach ($enabledFilters as $filter)
                @isset($filter['label'])
                    @php
                        $isBuilderPill = ($filter['source'] ?? null) === 'filterBuilder';
                        $pillClick = $isBuilderPill
                            ? "clearFilterBuilderRow(".intval($filter['index'] ?? 0).")"
                            : "clearFilter('".$filter['field']."')";
                    @endphp
                    <span
                        role="button"
                        data-cy="enabled-filters-clear-{{ $isBuilderPill ? 'fb-'.($filter['index'] ?? 0) : $filter['field'] }}"
                        wire:key="enabled-filters-{{ $isBuilderPill ? 'fb-'.($filter['index'] ?? 0) : $filter['field'] }}"
                        wire:click.prevent="{{ $pillClick }}"
                        class="{{ theme('header.enabled_filters.pill') }}"
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
            class="{{ theme('header.enabled_filters.pill_clear_all') }}"
        >
            {!! $element['iconHtml'] !!}
        </button>
    @endif
</div>
