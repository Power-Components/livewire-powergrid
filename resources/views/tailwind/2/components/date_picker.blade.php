@if(filled($date))
    @php
        $customConfig = [];
        if (isset($date['config'])) {
            foreach ($date['config'] as $key => $value) {
                $customConfig[$key] = $value;
            }
        }
    @endphp
    @if(!$inline)
        <div class="{!! ($date['class'] != '') ?? '' !!} pt-2 p-2" @if($inline) style="max-width: 370px;" @endif>
            @else
                <div class="pr-6" wire:ignore>
            @endif

            @if(!$inline)
                <label for="input_{!! (($column->data_field != '') ? $column->data_field: $date['field']) !!}">{!! $date['label'] !!}</label>
            @endif
            <input id="input_{!! (($column->data_field != '') ? $column->data_field: $date['field']) !!}"
                   data-key="filters.input_date_picker.date_picker.{!! (($column->data_field != '') ? $column->data_field: $date['field']) !!}"
                   wire:model="filters_value.date_picker.{!! (((($column->data_field != '') ? $column->data_field: $date['field']) != '') ? (($column->data_field != '') ? $column->data_field: $date['field']): $date['field']) !!}"
                   wire:ignore
                   class="range_input_{!! (($column->data_field != '') ? $column->data_field: $date['field']) !!} livewire_powergrid_input flatpickr flatpickr-input range_input_payment block appearance-no mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active
                       {{ (isset($class) != '') ? $class : 'w-full' }}"
                   type="text"
                   placeholder="{{ trans('livewire-powergrid::datatable.placeholders.select') }}"
            >
        </div>
        @push('powergrid_scripts')
            <script type="application/javascript">
                flatpickr(document.getElementsByClassName('range_input_{!! (($column->data_field != '') ? $column->data_field: $date['field']) !!}'), {
                        'mode': 'range',
                        'defaultHour' : 0,
                        ...@json(config('livewire-powergrid.plugins.flat_piker.locales.'.app()->getLocale())),
                        @if(isset($customConfig['only_future']))
                        "minDate": "today",
                        @endif
                            @if(isset($customConfig['no_weekends']) === true)
                        "disable": [
                            function (date) {
                                return (date.getDay() === 0 || date.getDay() === 6);
                            }
                        ],
                        @endif
                        ...@json($customConfig),
                        onClose: function (selectedDates, dateStr, instance) {
                            let data = [];
                            data.push({
                                selectedDates: selectedDates,
                                values: instance._input.attributes['data-key'].value
                            })
                            window.livewire.emit('inputDatePiker', data);
                        }
                    }
                );
            </script>
        @endpush
    @endif
