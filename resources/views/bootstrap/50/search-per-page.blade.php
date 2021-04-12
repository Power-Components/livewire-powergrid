<div class="dt--top-section">
    <div class="row">
        <div class="col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center">
            <div class="col-12">
                <label class="col-12 col-sm-3">
                    @if($perPage_input)
                        <select wire:model="perPage" class="livewire_powergrid_select col-12 col-sm-8 dropdown bootstrap-select form-control">
                            @foreach($perPageValues as $value)
                                <option value="{{$value}}"> @if($value == 0) Todos @else {{ $value }} @endif</option>
                            @endforeach
                        </select>
                    @endif
                </label>
                <span class="col-12">{{ trans('livewire-powergrid::datatable.labels.results_per_page') }}</span>
            </div>
        </div>
        <div class="col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3">
            <div class="col-12" style="text-align: right;">
                <label class="col-12 col-sm-8">
                    @if($search_input)
                    <div class="form-group has-search">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input wire:model.debounce.300ms="search" type="text" class="col-12 col-sm-8 form-control livewire_powergrid_input" placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}">
                    </div>
                    @endif
                </label>
            </div>
        </div>
    </div>
</div>

