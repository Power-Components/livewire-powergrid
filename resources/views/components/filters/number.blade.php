@props([
    'theme' => '',
    'inline' => null,
    'number' => null
])
<div>
    @if(filled($number))
        <div class="@if(!$inline) pt-2 p-2 @endif">
            @if(!$inline)
                <label>{{ $number['label'] }}</label>
            @endif
            <div class="@if($inline) flex flex-col @else flex flex-row @endif">

                <div class="@if(!$inline) pt-2 p-2 @endif">
                    <input
                        data-id="{{ $number['field'] }}"
                        wire:input.debounce.800ms="filterNumberStart('{{ $number['data_field'] }}', $event.target.value,'{{ addslashes($number['thousands']) }}','{{ addslashes($number['decimal']) }}','{{ $number['label'] }}')"
                        @if($inline) style="min-width: 100px; max-width: 130px !important;" @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }}"
                        placeholder="MIN">
                </div>
                <div class="@if(!$inline) mt-1 @else pt-1 @endif">
                    <input
                        data-id="{{ $number['field'] }}"
                        wire:input.debounce.800ms="filterNumberEnd('{{ $number['data_field'] }}',$event.target.value,'{{ addslashes($number['thousands']) }}','{{ addslashes($number['decimal']) }}', '{{ $number['label'] }}')"
                        @if($inline) style="min-width: 100px; max-width: 130px !important;" @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }}"
                        placeholder="MAX">
                </div>
            </div>
        </div>
    @endif


</div>
