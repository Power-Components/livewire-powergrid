@if(!is_array($data))
    <footer class="mt-50 pb-1 w-100 align-items-end px-1 d-flex flex-wrap">
        <div class="col-auto me-auto">
            @if($perPageInput)
                <div class="d-flex justify-content-center align-items-center">
                    <label class="w-auto">
                        <select wire:model="perPage" class="form-select pe-3">
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
                    <small class="ms-50">
                        {{ trans('livewire-powergrid::datatable.labels.results_per_page') }}
                    </small>
                </div>
            @endif
        </div>
        <div class="col-auto overflow-auto mt-1 mt-sm-0">
            @if(method_exists($data, 'links'))
                {!! $data->links(powerGridThemeRoot().'.pagination', ['recordCount' => $recordCount]) !!}
            @endif
        </div>
    </footer>
@endif
