@if($searchInput)
    <div class="flex flex-row w-full flex justify-end">
        <div class="relative w-4/12 float-end float-right">
              <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                 <span class="p-1 focus:outline-none focus:shadow-outline">
                    <x-livewire-powergrid::icons.search/>
                 </span>
              </span>
            <input wire:model.debounce.300ms="search" type="text"
                   class="block w-full float-right bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 pl-10"
                   placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}">
        </div>
    </div>
@endif

