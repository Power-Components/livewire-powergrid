<div class="flex flex-row w-full flex justify-between">
    @if($perPage_input)
        <div class="flex flex-row">
            <button
                class="block w-full float-right bg-gray-200 text-gray-700 border border-gray-100 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 items-center inline-flex"
                wire:click="exportToExcel()"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-arrow-down-circle" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"></path>
                </svg>
                <span style="padding-left: 6px">
                    {!! (count($checkbox_values)) ? trans('livewire-powergrid::datatable.buttons.export_selected') :
                         trans('livewire-powergrid::datatable.buttons.export') !!}
                    </span>
            </button>
            <div style="min-width: 60px;">
                <div wire:loading class="pl-3 pr-2 flex flex-col items-right justify-center">
                    <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-8 w-8 pt-4"></div>
                </div>
            </div>
        </div>
    @endif
    @if($search_input)
        <div class="flex flex-row w-full flex justify-end">
            <div class="relative w-4/12 float-end float-right">
              <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                 <span class="p-1 focus:outline-none focus:shadow-outline">
                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                         stroke-width="2"
                         viewBox="0 0 24 24" class="w-6 h-6 text-gray-400">
                       <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                 </span>
              </span>
                <input wire:model.debounce.300ms="search" type="text"
                       class="block w-full float-right bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 pl-10"
                       placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}">
            </div>
        </div>

    @endif
</div>
