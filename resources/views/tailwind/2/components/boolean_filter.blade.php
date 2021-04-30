@if(filled($boolean_filter))
<div wire:ignore class="@if($inline) pr-6 @endif{!! ($boolean_filter['class'] != '') ?? '' !!} pt-2 p-2">
    @if(!$inline)
    <label for="input_boolean_filter_{!! $boolean_filter['field']!!}">{{$boolean_filter['label']}}</label>
    @endif
    <div class="relative">
        <select id="input_boolean_filter_{!! $boolean_filter['field']!!}" class="appearance-none livewire_powergrid_input flatpickr flatpickr-input block appearance-no mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active
         {{ (isset($class)) ? $class : 'w-9/12' }}"

            wire:input.debounce.800ms="filterBoolean('{{ $boolean_filter['data_field'] }}', $event.target.value)" wire:ignore data-live-search="{{ $boolean_filter['live-search'] }}">
            <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
            <option value="true">{{ $boolean_filter['true_label']}}</option>
            <option value="false">{{ $boolean_filter['false_label']}}</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path>
            </svg>
        </div>
    </div>
</div>
@endif
