@php
    $__partial = $__partial ?? $this;
    $enabledFilters = $enabledFilters ?? $__partial->enabledFilters;
@endphp

<div
    wire:partial="pg-enabled-filters-{{ $__partial->tableName }}"
    @class(['pg-enabled-filters-base flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-zinc-200 dark:border-zinc-700' => count($enabledFilters)])
    @if (count($enabledFilters)) data-cy="enabled-filters" @endif
>
    @if (count($enabledFilters))
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                {{ trans('livewire-powergrid::datatable.labels.active_filters') }}
            </span>

            @foreach ($enabledFilters as $filter)
                @isset($filter['label'])
                    @php
                        $isBuilderPill = ($filter['source'] ?? null) === 'filterBuilder';
                        $pillKey = $isBuilderPill ? 'fb-'.($filter['index'] ?? 0) : $filter['field'];
                        $pillClick = $isBuilderPill
                            ? 'clearFilterBuilderRow('.intval($filter['index'] ?? 0).')'
                            : "clearFilter('".$filter['field']."')";
                    @endphp
                    <flux:badge
                        wire:key="enabled-filters-{{ $pillKey }}"
                        data-cy="enabled-filters-clear-{{ $pillKey }}"
                        size="sm"
                        variant="outline"
                    >
                        {{ $filter['label'] }}
                        <flux:badge.close wire:click.prevent="{{ $pillClick }}" />
                    </flux:badge>
                @endisset
            @endforeach
        </div>

        <button
            type="button"
            data-cy="enabled-filters-clear-all"
            wire:click.prevent="clearAllFilters"
            aria-label="{{ trans('livewire-powergrid::datatable.buttons.clear_all_filters') }}"
            class="cursor-pointer text-zinc-400 transition hover:text-zinc-600 dark:hover:text-zinc-200"
        >
            <x-livewire-powergrid::icons.x class="w-4 h-4" />
        </button>
    @endif
</div>
