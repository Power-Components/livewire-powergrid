<select wire:model.lazy="perPage" class="{{ $theme->footer->selectClass }}">
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


