@if (data_get($setUp, 'header.searchInput'))
    <label class="input">
        <x-livewire-powergrid::icons.search class="{{ theme_style($theme, 'searchBox.iconSearch') }}" />

        <input
            wire:model.live.debounce.700ms="search"
            type="search"
            class="{{ theme_style($theme, 'searchBox.input') }}"
            placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}"
        />

        @if ($search)
            <span class="absolute opacity-0 group-hover:opacity-100 transition-all inset-y-0 right-0 flex items-center">
                <span class="p-2 rounded-full focus:outline-none focus:shadow-outline cursor-pointer">
                    <a
                        class="cursor-pointer"
                        wire:click.prevent="$set('search','')"
                    >
                        <x-livewire-powergrid::icons.x
                            class="w-4 h-4 {{ theme_style($theme, 'searchBox.iconClose') }}" />
                    </a>
                </span>
            </span>
        @endif

    </label>
@endif
