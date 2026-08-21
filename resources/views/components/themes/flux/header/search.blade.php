@if (data_get($setUp, 'header.searchInput'))
    @php($element = ($__partial ?? $this)->headerElement('search'))
    <div class="{{ theme('header.search_box.container') }}">
        <div class="{{ theme('header.search_box.relative_main') }}">
            @if ($element['isComponentPath'] && $element['iconHtml'])
                <flux:input
                    wire:model.live.debounce.700ms="search"
                    wire:partial.ignore="pg-search-{{ $tableName }}"
                    type="text"
                    placeholder="{{ $element['title'] }}"
                    clearable
                >
                    <x-slot name="iconLeading">
                        {!! $element['iconHtml'] !!}
                    </x-slot>
                </flux:input>
            @else
                <flux:input
                    wire:model.live.debounce.700ms="search"
                    wire:partial.ignore="pg-search-{{ $tableName }}"
                    type="text"
                    :icon="$element['icon'] ?: null"
                    placeholder="{{ $element['title'] }}"
                    clearable
                />
            @endif
        </div>
    </div>
@endif
