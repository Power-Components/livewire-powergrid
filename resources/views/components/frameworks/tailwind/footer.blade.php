<div class="md:flex md:flex-row w-full justify-between pt-3 bg-white overflow-y-auto pl-2 pr-2 pb-1 relative dark:bg-gray-700">

    @if($perPageInput)
        <div class="flex flex-row justify-center md:justify-start mb-2 md:mb-0">
            <div class="relative h-10">

                <select wire:model.lazy="perPage"
                        class="block appearance-none bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500  dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500">
                    @foreach($perPageValues as $value)
                        <option value="{{$value}}">
                            @if($value == 0)
                                {{ trans('livewire-powergrid::datatable.labels.all') }}
                            @else
                                {{ $value }}
                            @endif
                        </option>
                    @endforeach
                </select>

                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            <div class="pl-4 hidden sm:block md:block lg:block w-full" style="padding-top: 6px;">
            </div>
        </div>
    @endif

    @if(filled($data))
        <div>
            @if(method_exists($data, 'links'))
                {!! $data->links($theme->base. ''.$theme->name.'.pagination', ['recordCount' => $recordCount]) !!}
            @endif
        </div>
    @endif
</div>


