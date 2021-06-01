@props([
    'theme' => '',
    'inline' => null,
    'booleanFilter' => null
])
<div>
    @if(filled($booleanFilter))
        <div wire:ignore class="@if($inline) {{ $theme->divClassInline }} @endif {{ $theme->divClassNotInline }}{!! ($booleanFilter['class'] != '') ?? '' !!}">
            @if(!$inline)
                <label for="input_boolean_filter_{!! $booleanFilter['field']!!}">{{$booleanFilter['label']}}</label>
            @endif
            <div class="relative">
                <select id="input_boolean_filter_{!! $booleanFilter['field']!!}" class="{{ $theme->inputClass }} {{ (isset($class)) ? $class : 'w-9/12' }}"

                        wire:input.debounce.800ms="filterBoolean('{{ $booleanFilter['data_field'] }}', $event.target.value)" wire:ignore data-live-search="{{ $booleanFilter['live-search'] }}">
                    <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
                    <option value="true">{{ $booleanFilter['true_label']}}</option>
                    <option value="false">{{ $booleanFilter['false_label']}}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path>
                    </svg>
                </div>
            </div>
        </div>
    @endif
</div>
