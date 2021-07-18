@if(!is_array($data))
    <div class="d-flex justify-content-between">

        @if($perPageInput)
            <div class="d-flex justify-content-center">
                <div>
                    <label class="col-12 col-sm-6 col-md-6" style="width: 120px;">
                        <select wire:model="perPage" class="form-select" style="width: 110px;">
                            @foreach($perPageValues as $value)
                                <option value="{{$value}}">
                                    @if($value == 0)
                                        {{ trans('livewire-powergrid::datatable.labels.all') }}
                                    @else {{ $value }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <span style="padding-top: 8px;padding-left: 6px;">{{ trans('livewire-powergrid::datatable.labels.results_per_page') }}</span>
            </div>
        @endif
        <div>
            {!! $data->links($theme->base. ''.$theme->name.'.pagination') !!}
        </div>
    </div>
@endif
