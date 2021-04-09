@if(filled($date))
    @php
        $customConfig = [];
        if (isset($date['config'])) {
            foreach ($date['config'] as $key => $value) {
                $customConfig[$key] = $value;
            }
        }
    @endphp
        <div class="@if(!$inline) col-md-6 col-lg-3 @endif {!! ($date['class'] != '') ?? '' !!} pt-2" style="max-width: 370px !important;">

            @if(!$inline)
                <label for="input_{!! $date['from_column'] !!}">{!! $date['label'] !!}</label>
            @endif
            <input id="input_{!! $date['from_column'] !!}"
                   data-key="filters.date_picker.{!! $date['from_column'] !!}"
                   wire:model="filters.input_date_picker.{!! $date['from_column'] !!}"
                   wire:ignore
                   class="livewire_powergrid_input flatpickr flatpickr-input range_input_{!! $date['from_column'] !!} form-control active
                   {{ (isset($class) != '') ? $class :  '' }}"
                   type="text"
                   placeholder="Selecione o período.."
            >
        </div>
        @push('powergrid_scripts')
            <script type="application/javascript">
                flatpickr(document.getElementsByClassName('range_input_{!! $date['from_column'] !!}'), {
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
                                selectedDates: dateStr,
                                values: instance._input.attributes['data-key'].value
                            })
                            $('.spinner').html('<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>')
                            window.livewire.emit('inputDatePiker', data);
                        }
                    }
                );

            </script>
        @endpush
    @endif
