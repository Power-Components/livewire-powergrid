@props([
    'theme' => '',
    'inline' => null,
    'date' => null
])
<div>
    @php
        $customConfig = [];
        if (isset($date['config'])) {
            foreach ($date['config'] as $key => $value) {
                $customConfig[$key] = $value;
            }
        }
    @endphp

    <div class="{{ $theme->divClassNotInline }}" @if($inline)  @endif>

        @if(!$inline)
            <label for="input_{!! $date['field'] !!}"
                   class="text-gray-700 dark:text-gray-300">{!! $date['label'] !!}</label>
        @endif
        <input id="input_{!! $date['field'] !!}"
               data-field="{!! $date['dataField'] !!}"
               data-key="enabledFilters.date_picker.{!! $date['dataField'] !!}"
               class="power_grid range_input_{!! $date['field'] !!} {{ $theme->inputClass }}"
               type="text"
               placeholder="{{ trans('livewire-powergrid::datatable.placeholders.select') }}"
                wire:model="filters.input_date_picker.{!!$date['dataField'] !!}"
               wire:ignore
        >
    </div>
    @push('power_grid_scripts')
        <script type="application/javascript">
            flatpickr(document.getElementsByClassName('range_input_{!! $date['field'] !!}'), {
                    mode: 'range',
                    defaultHour: 0,
                    ...@json(config('livewire-powergrid.plugins.flat_piker.locales.'.app()->getLocale())),
                    @if(isset($customConfig['only_future']))
                    minDate: 'today',
                    @endif
                        @if(isset($customConfig['no_weekends']) === true)
                    disable: [
                        function (date) {
                            return (date.getDay() === 0 || date.getDay() === 6);
                        }
                    ],
                    @endif
                    ...@json($customConfig),
                    onClose: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            window.livewire.emit('eventChangeDatePiker', {
                                selectedDates: selectedDates,
                                field: instance._input.attributes['data-field'].value,
                                values: instance._input.attributes['data-key'].value,
                                label: '{{ $date['label'] }}'
                            });
                        }
                    }
                }
            );
        </script>
    @endpush

</div>

