@if(filled($number))
    <div>
        @if(!$inline)
            <label>{{ $number['label'] }}</label>
        @endif
            <div class="@if($inline) flex flex-col @else flex flex-row @endif">
                <div class="mt-1 mb-1 @if(!$inline) pr-4 @endif">
                    <input
                        data-id="{{ $number['field'] }}"
                        wire:model.debounce.800ms="filters_enabled.{{ $column->field }}"
                        wire:input.debounce.800ms="filterInputText('{{ $number['field'] }}', $event.target.value)"
                        type="text"
                        class="form-control"
                        placeholder="{{ $column->title }}">
                </div>
            </div>
    </div>
@endif

