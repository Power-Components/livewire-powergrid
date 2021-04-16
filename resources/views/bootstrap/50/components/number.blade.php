
@if(filled($number))
    <div @if(!$inline) class="col-md-6 col-lg-3 pt-2" @endif>
        @if(!$inline)
            <label>{{$number['label']}}</label>
        @endif
            <div @if(!$inline) class="input-group mb-3" @endif>
                <input wire:input.debounce.500ms="filterNumberStart('{{ ($number['with'] != '') ? $number['with']: $number['field'] }}', $event.target.value)"  style="@if($inline) max-width: 130px !important; margin-bottom: 6px; @else margin-right: 12px;padding-bottom: 6px;@endif" type="number" class="form-control livewire_powergrid_input" placeholder="MIN">
                <input wire:input.debounce.500ms="filterNumberEnd('{{ ($number['with'] != '') ? $number['with']: $number['field'] }}', $event.target.value)" style="@if($inline) max-width: 130px !important; @endif" type="number" class="form-control livewire_powergrid_input" placeholder="MAX">
            </div>

    </div>
@endif
