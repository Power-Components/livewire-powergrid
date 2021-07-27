@props([
    'theme' => '',
    'inline' => null,
    'number' => null
])
<div>
    @if(filled($number))
        <div class="@if(!$inline) pt-2 p-2 @endif">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300">{{ $number['label'] }}</label>
            @endif
            <div class="@if($inline) flex flex-col @else  flex flex-row justify-between @endif">

                <div class="@if(!$inline) pl-0 pt-1 pr-3 @endif">
                    <input
                        data-id="{{ $number['field'] }}"
                        wire:input.debounce.800ms="filterNumberStart('{{ $number['dataField'] }}', $event.target.value,'{{ addslashes($number['thousands']) }}','{{ addslashes($number['decimal']) }}','{{ $number['label'] }}')"
                        @if($inline) style="min-width: 100px; max-width: 130px !important;" @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }}"
                        placeholder="MIN">
                </div>
                <div class="@if(!$inline) mt-1 @else pt-1 @endif">
                    <input
                        data-id="{{ $number['field'] }}"
                        wire:input.debounce.800ms="filterNumberEnd('{{ $number['dataField'] }}',$event.target.value,'{{ addslashes($number['thousands']) }}','{{ addslashes($number['decimal']) }}', '{{ $number['label'] }}')"
                        @if($inline) style="min-width: 100px; max-width: 130px !important;" @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }}"
                        placeholder="MAX">
                </div>
            </div>
        </div>
    @endif


</div>
