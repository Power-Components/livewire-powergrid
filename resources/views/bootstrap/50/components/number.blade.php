@if(filled($number))
    <div @if(!$inline) class="col-md-6 col-lg-3 pt-2" @endif>
        @if(!$inline)
            <label>{{$number['label']}}</label>
        @endif
        <div @if(!$inline) class="input-group mb-3" @endif>
            <input data-id="{{ $number['field'] }}"
                   wire:model.debounce.800ms="filters_enabled.{{ $number['field'] }}.start"
                   wire:input.debounce.800ms="filterNumberStart('{{ $number['dataField'] }}', $event.target.value, '{{ addslashes($number['thousands']) }}', '{{ addslashes($number['decimal']) }}')"
                   style="@if($inline) max-width: 130px !important; @else margin-right: 12px;padding-bottom: 6px;@endif"
                   type="number" class="form-control livewire_powergrid_input" placeholder="MIN">
            <input data-id="{{ $number['field'] }}"
                   wire:model.debounce.800ms="filters_enabled.{{ $number['field'] }}.end"
                   wire:input.debounce.800ms="filterNumberEnd('{{ $number['dataField'] }}', $event.target.value, '{{ addslashes($number['thousands']) }}', '{{ addslashes($number['decimal']) }}')"
                   style="@if($inline) max-width: 130px !important; @endif" type="number"
                   class="form-control livewire_powergrid_input" placeholder="MAX">
        </div>

    </div>
@endif
