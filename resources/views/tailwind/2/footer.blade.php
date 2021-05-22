<div class="flex flex-row w-full flex justify-between pt-3 bg-white overflow-y-auto relative">

    @include('livewire-powergrid::tailwind.2.per-page')

    @if(filled($data))
        <div>
            @if(method_exists($data, 'links'))
                {!! $data->links('livewire-powergrid::tailwind.2.pagination', ['record_count' => $record_count]) !!}
            @endif
        </div>
    @endif
</div>
