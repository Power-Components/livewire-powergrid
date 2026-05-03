@if (data_get($setUp, 'header.searchInput'))
    <div class="{{ theme('search_box.container') }}">
        <div class="{{ theme('search_box.relative_main') }}">
            <span class="{{ theme('search_box.icon_search_wrapper') }}">
                <x-livewire-powergrid::icons.search
                    class="{{ theme('search_box.icon_search') }}"
                />
            </span>
            <input
                wire:model.live.debounce.700ms="search"
                type="text"
                class="{{ theme('search_box.input') }}"
                placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}"
            >
            @if ($search)
                <span class="{{ theme('search_box.icon_close_wrapper') }}">
                    <a wire:click.prevent="$set('search','')" class="p-1 cursor-pointer rounded-full focus:outline-none">
                        <x-livewire-powergrid::icons.x
                            class="w-4 h-4 {{ theme('search_box.icon_close') }}"
                        />
                    </a>
                </span>
            @endif
        </div>
    </div>
@endif
