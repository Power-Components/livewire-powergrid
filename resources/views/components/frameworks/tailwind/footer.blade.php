<div class="flex flex-row w-full flex justify-between pt-3 bg-white overflow-y-auto relative">

    @if($perPageInput)
        <div class="flex flex-row">
            <div class="relative h-10">

                @include('livewire-powergrid::components.per-page')
                <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20">
                        <path
                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            <div class="pl-4 hidden sm:block md:block lg:block w-full"
                 style="padding-top: 6px;">
            </div>
        </div>
    @endif

    @if(filled($data))
        <div>
            @if(method_exists($data, 'links'))
                {!! $data->links('livewire-powergrid::components.frameworks.tailwind.pagination', ['record_count' => $record_count]) !!}
            @endif
        </div>
    @endif
</div>


