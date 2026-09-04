@if (data_get($setUp, 'header.searchInput'))
    @php($element = ($__partial ?? $this)->headerElement('search'))
    @php($clearElement = ($__partial ?? $this)->headerElement('searchClear'))
    <div class="{{ theme('header.search_box.container') }}">
        <label class="{{ theme('header.search_box.relative_main') }}">
            <span class="{{ theme('header.search_box.icon_search_wrapper') }}">
                {!! $element['iconHtml'] !!}
            </span>
            <input
                wire:model.live.debounce.700ms="search"
                wire:partial.ignore="pg-search-{{ $tableName }}"
                type="text"
                class="{{ theme('header.search_box.input') }}"
                placeholder="{{ $element['title'] }}"
            >
            @if ($search)
                <span class="{{ theme('header.search_box.icon_close_wrapper') }}">
                    <a wire:click.prevent="$set('search','')" class="p-1 cursor-pointer rounded-full focus:outline-none">
                        {!! $clearElement['iconHtml'] !!}
                    </a>
                </span>
            @endif
        </label>
    </div>
@endif
