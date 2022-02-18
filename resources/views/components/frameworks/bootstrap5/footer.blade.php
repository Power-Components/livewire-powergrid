@if(!is_array($data))
    <footer class="mt-50 pb-1 w-100 align-items-end px-1 d-flex flex-wrap justify-content-center justify-content-md-between">
        <div class="col-auto overflow-auto mt-1 mt-sm-0 bs5-per-page">
            @if($perPageInput)
                <div class="d-flex flex-column flex-lg-row align-items-center">
                    <label class="w-auto">
                        <select wire:model="perPage" class="form-select">
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
                    <small class="ms-2 text-muted">
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
    <style>
        @media (min-width: 992px) {
            .bs5-per-page {
                height: 38px
            }
        }
    </style>
@endif
