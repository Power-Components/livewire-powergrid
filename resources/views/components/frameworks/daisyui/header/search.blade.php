@if (data_get($setUp, 'header.searchInput'))
    <div class="{{ theme('header.searchBox.container') }}">
        <label class="input {{ theme('header.searchBox.input') }}">
            <x-livewire-powergrid::icons.search class="h-[1em] opacity-50" />
            <input 
                wire:model.live.debounce.700ms="search" 
                type="search" 
                class="grow" 
                placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}" 
            />
            @if ($search)
                <a wire:click.prevent="$set('search','')">
                    <x-livewire-powergrid::icons.x class="w-4 h-4 cursor-pointer opacity-50 hover:opacity-100 transition-opacity" />
                </a>
            @endif
        </label>
    </div>
@endif