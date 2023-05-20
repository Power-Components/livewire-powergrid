@if (data_get($setUp, 'header.searchInput'))
    <div class="flex flex-row mt-2 md:mt-0 w-full rounded-full flex justify-start sm:justify-center md:justify-end">
        <div class="group relative rounded-full w-full md:w-4/12 float-end float-right md:w-full lg:w-1/2">
            <span class="absolute inset-y-0 left-0 flex items-center pl-1">
                <span class="p-1 focus:outline-none focus:shadow-outline">
                    <x-livewire-powergrid::icons.search
                        class="{{ $theme->searchBox->iconSearchClass }}"
                        style="{{ $theme->searchBox->iconSearchStyle }}"
                    />
                </span>
            </span>
            <input
                wire:model.debounce.700ms="search"
                type="text"
                class="{{ $theme->searchBox->inputClass }}"
                style="{{ $theme->searchBox->inputStyle }}"
                placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}"
            >
            @if ($search)
                <span
                    class="absolute opacity-0 group-hover:opacity-100 transition-all inset-y-0 right-0 flex items-center"
                >
                    <span class="p-2 rounded-full focus:outline-none focus:shadow-outline cursor-pointer">
                        <a wire:click.prevent="$set('search','')">
                            <x-livewire-powergrid::icons.x
                                class="w-4 h-4 {{ $theme->searchBox->iconCloseClass }}"
                                style="{{ $theme->searchBox->iconCloseStyle }}"
                            />
                        </a>
                    </span>
                </span>
            @endif
        </div>
    </div>
@endif
