@if (data_get($setUp, 'header.searchInput'))
    <div class="{{ theme('header.search_box.container') }}">
        <div class="{{ theme('header.search_box.relative_main') }}">
            <span class="{{ theme('header.search_box.icon_search_wrapper') }}">
                <x-livewire-powergrid::icons.search
                    class="{{ theme('header.search_box.icon_search') }}"
                />
            </span>
            <input
                wire:model.live.debounce.700ms="search"
                type="text"
                class="{{ theme('header.search_box.input') }}"
                placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}"
            >
            @if ($search)
                <span class="{{ theme('header.search_box.icon_close_wrapper') }}">
                    <a wire:click.prevent="$set('search','')" class="p-1 cursor-pointer rounded-full focus:outline-none">
                        <x-livewire-powergrid::icons.x
                            class="w-4 h-4 {{ theme('header.search_box.icon_close') }}"
                        />
                    </a>
                </span>
            @endif
        </div>
    </div>
@endif
