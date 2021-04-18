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
                        class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        placeholder="{{ $column->title }}">
                </div>
            </div>
    </div>
@endif

