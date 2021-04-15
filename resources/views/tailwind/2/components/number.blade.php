
@if(filled($number))
    <div class="@if($inline) pr-6 @endif pt-2 p-2">
        @if(!$inline)
            <label>{{$number['label']}}</label>
        @endif
            <div class="@if($inline) flex flex-col @else flex flex-row @endif">
                <div class="mt-1 mb-1 @if(!$inline) pr-4 @endif">
                    <input wire:input.debounce.500ms="filterNumberStart('{{ $number['field'] }}', $event.target.value)" @if($inline) style="max-width: 130px !important;" @endif type="number"
                           class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                           placeholder="MIN">
                </div>
                <div class="mt-1 mb-1">
                    <input wire:input.debounce.500ms="filterNumberEnd('{{ $number['field'] }}', $event.target.value)" @if($inline) style="max-width: 130px !important;" @endif type="number"
                           class="w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                           placeholder="MAX">
                </div>
            </div>
    </div>
@endif

