<div class="flex flex-row w-full flex justify-between">

    <div class="flex flex-row">

        @if($export_option)
            <div x-data="{show: false}"
                 @click.away="show = false">

                <button @click="show = ! show"
                        class="block w-full bg-white-200 text-gray-700 border border-gray-300 rounded py-1.5 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <div class="flex">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                         class="fill-current text-gray-400" viewBox="0 0 16 16">
                      <path
                          d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                      <path
                          d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                    </svg>
                </span>

                        <svg class="fill-current text-gray-400" xmlns="http://www.w3.org/2000/svg" height="24"
                             viewBox="0 0 24 24" width="24">
                            <path d="M7 10l5 5 5-5z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>

                    </div>

                </button>

                <div x-show="show"
                     x-transition:enter="transform duration-200"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transform duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-90"
                     class="mt-2 py-2 w-48 bg-white shadow-xl absolute z-10">

                    @if(in_array('excel',$export_type))
                        <a wire:click="exportToExcel()" href="#"
                           class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-black-200">Excel</a>
                    @endif
                    @if(in_array('csv',$export_type))
                        <a wire:click="exportToCsv()" href="#"
                           class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-black-200">Csv</a>
                    @endif
                </div>

            </div>
        @endif

        <div style="min-width: 60px;">
            <div wire:loading class="px-3 py-1 flex flex-col items-right justify-center">
                <div class="loader ease-linear rounded-full border-2 border-t-2 border-gray-200 h-6 w-6 pt-2"></div>
            </div>
        </div>

    </div>

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
