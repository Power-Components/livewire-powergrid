@if(filled($number))
    <div class="@if($inline) pr-6 @endif{!! ($select['class'] != '') ?? '' !!} pt-2 p-2">
        @if(!$inline)
            <label>{{ $number['label'] }}</label>
        @endif
            <div class="@if($inline) flex flex-col @else flex flex-row @endif">
                <div class="mt-1 mb-1 @if(!$inline) pr-4 @endif">
                    <input
                        data-id="{{ $number['field'] }}"
                        wire:model.debounce.800ms="filters_enabled.{{ $number['field'] }}.start"
                        wire:input.debounce.800ms="filterNumberStart('{{ $number['data_field'] }}', $event.target.value, '{{ addslashes($number['thousands']) }}', '{{ addslashes($number['decimal']) }}')"
                        @if($inline) style="min-width: 100px; max-width: 130px !important;" @endif
                        type="text"
                        class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        placeholder="MIN">
                </div>
                <div class="mt-1 @if(!$inline)mb-1 pr-4 @endif">
                    <input
                        data-id="{{ $number['field'] }}"
                        wire:model.debounce.800ms="filters_enabled.{{ $number['field'] }}.end"
                        wire:input.debounce.800ms="filterNumberEnd('{{ $number['data_field'] }}', $event.target.value, '{{ addslashes($number['thousands']) }}', '{{ addslashes($number['decimal']) }}')"
                        @if($inline) style="min-width: 100px; max-width: 130px !important;" @endif
                        type="text"
                        class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        placeholder="MAX">
                </div>
            </div>
    </div>
@endif

