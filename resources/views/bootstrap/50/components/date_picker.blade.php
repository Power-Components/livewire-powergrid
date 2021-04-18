@if(filled($date))
    @php
        $customConfig = [];
        if (isset($date['config'])) {
            foreach ($date['config'] as $key => $value) {
                $customConfig[$key] = $value;
            }
        }
    @endphp

    <div class="@if(!$inline) col-md-6 col-lg-3 @endif {!! ($date['class'] != '') ?? '' !!} pt-2"
         style="max-width: 370px !important;">

        @if(!$inline)
            <label for="input_{!! $date['field'] !!}">{!! $date['label'] !!}</label>
        @endif
        <input id="input_{!! $date['field'] !!}"
               data-key="filters.date_picker.{!!$date['field'] !!}"
               wire:model="filters.input_date_picker.{!!$date['field'] !!}"
               wire:ignore
               class="livewire_powergrid_input flatpickr flatpickr-input range_input_{!!$date['field'] !!} form-control active
                   {{ (isset($class) != '') ? $class :  '' }}"
               type="text"
               placeholder="{{ trans('livewire-powergrid::datatable.placeholders.select') }}"
        >
    </div>
    @push('powergrid_scripts')
        <!-- Power Grid Date Picker Scripts -->
        <script type="application/javascript">
            flatpickr(document.getElementsByClassName('range_input_{!!$date['field'] !!}'), {
                    'mode': 'range',
                    'defaultHour': 0,
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
                        window.livewire.emit('eventChangeDatePiker', {
                            selectedDates: selectedDates,
                            values: instance._input.attributes['data-key'].value
                        });
                    }
                }
            );
        </script>
        <!-- Power Grid Date Picker Scripts -->
    @endpush
@endif
