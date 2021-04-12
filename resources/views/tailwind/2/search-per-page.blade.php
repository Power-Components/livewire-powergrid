<div class="w-full flex pb-2 pt-2">
    @if($perPage_input)
        <div class="relative w-4/12 sm:w-4/12 md:w-2/12 lg:2/12">
            <select wire:model="perPage" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="grid-state">
                @foreach($perPageValues as $value)
                    <option value="{{$value}}"> @if($value == 0) Todos @else {{ $value }} @endif</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
            </div>
        </div>
        <span class="pl-4 hidden sm:block md:block lg:block w-full" style="padding-top: 10px;">{{ trans('livewire-powergrid::datatable.labels.results_per_page') }}</span>
    @endif
    @if($search_input)
      <div class="relative w-10/12 sm:w-8/12 md:w-8/12 mx-1 float-end float-right">
      <span class="absolute inset-y-0 left-0 flex items-center pl-2">
         <span class="p-1 focus:outline-none focus:shadow-outline">
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 h-6 text-gray-400">
               <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
         </span>
      </span>
            <input wire:model.debounce.300ms="search" type="text" class="appearance-none block w-full float-right bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 pl-10" placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}">
        </div>
    @endif
</div>
