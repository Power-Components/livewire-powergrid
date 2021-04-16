@if(filled($number))
    <div>
        @if(!$inline)
            <label>{{ $number['label'] }}</label>
        @endif
            <div class="@if($inline) flex flex-col @else flex flex-row @endif">
                <div class="mt-1 mb-1 @if(!$inline) pr-4 @endif">
                    <input
                        data-id="{{ (($column->data_field != '') ? $column->data_field: $number['field']) }}"
                        wire:input.debounce.500ms="filterNumberStart('{{ (($column->data_field != '') ? $column->data_field: $number['field']) }}', $event.target.value, '{{ addslashes($number['decimal']) }}', '{{ addslashes($number['thousands']) }}')" @if($inline)
                        style="max-width: 130px !important;" @endif
                        type="text"
                        class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        placeholder="MIN">
                </div>
                <div class="mt-1 mb-1">
                    <input
                        data-id="{{ (($column->data_field != '') ? $column->data_field: $number['field']) }}"
                        wire:input.debounce.500ms="filterNumberEnd('{{ (($column->data_field != '') ? $column->data_field: $number['field']) }}', $event.target.value, '{{ addslashes($number['decimal']) }}', '{{ addslashes($number['thousands']) }}')" @if($inline)
                        style="max-width: 130px !important;" @endif
                        type="text"
                        class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        placeholder="MAX">
                </div>
            </div>
    </div>
@endif

