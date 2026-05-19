@if (data_get($setUp, 'header.searchInput'))
    <div class="{{ theme('header.search_box.container') }}">
        <div class="{{ theme('header.search_box.relative_main') }}">
            <flux:input
                wire:model.live.debounce.700ms="search"
                wire:partial.ignore="pg-search-{{ $tableName }}"
                type="text"
                icon="magnifying-glass"
                placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}"
                clearable
            />
        </div>
    </div>
@endif
