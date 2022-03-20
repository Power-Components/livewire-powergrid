@if($searchInput)
    <div class="flex flex-row mt-2 md:mt-0 w-full flex justify-start sm:justify-center md:justify-end">

        <div class="relative w-full md:w-4/12 float-end float-right md:w-full lg:w-1/2">
              <span class="absolute inset-y-0 left-0 flex items-center pl-1">
                 <span class="p-1 focus:outline-none focus:shadow-outline">
                    <x-livewire-powergrid::icons.search class="text-gray-300 dark:text-gray-200"/>
                 </span>
              </span>
            <input wire:model.debounce.600ms="search" type="text"
                   style="padding-left: 36px !important;"
                   class="block w-full float-right bg-gray-50 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 pl-10 dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500"
                   placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}">
            @if($search)
                <span class="absolute inset-y-0 right-0 flex items-center pl-1">
                    <span class="p-1 focus:outline-none focus:shadow-outline cursor-pointer">
                        <a wire:click.prevent="$set('search','')">
                            <x-livewire-powergrid::icons.x class="text-gray-300 dark:text-gray-200"/>
                        </a>
                    </span>
                </span>
            @endif
        </div>

    </div>
@endif
