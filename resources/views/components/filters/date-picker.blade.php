@props([
    'theme' => '',
    'inline' => null,
    'date' => null,
    'column' => null,
])
<div>
    @php
        $customConfig = [];
        if (data_get($date, 'config')) {
            foreach (data_get($date, 'config') as $key => $value) {
                $customConfig[$key] = $value;
            }
        }
    @endphp

    <div class="{{ $theme->divClassNotInline }}" @if($inline)  @endif>

        @if(!$inline)
            <label for="input_{{ data_get($date, 'field') }}"
                   class="text-gray-700 dark:text-gray-300">
                {{ data_get($date, 'label') }}
            </label>
        @endif
        <input id="input_{{ data_get($date, 'field') }}"
               data-field="{{ data_get($date, 'dataField') }}"
               style="{{ data_get($column, 'headerStyle') }}"
               data-key="enabledFilters.date_picker.{{ data_get($date, 'dataField') }}"
               class="power_grid range_input_{{ data_get($date, 'dataField') }} {{ $theme->inputClass }} {{ data_get($column, 'headerClass') }}"
               type="text"
               placeholder="{{ trans('livewire-powergrid::datatable.placeholders.select') }}"
               wire:model="filters.input_date_picker.{{ data_get($date, 'dataField') }}"
               wire:ignore>
    </div>
    @push('power_grid_scripts')
        <script type="application/javascript">
            flatpickr(document.getElementsByClassName('range_input_{{ data_get($date, 'dataField') }}'), {
                    mode: 'range',
                    defaultHour: 0,
                    ...@json(config('livewire-powergrid.plugins.flat_piker.locales.'.app()->getLocale())),
                    @if(data_get($customConfig, 'only_future'))
                    minDate: 'today',
                    @endif
                        @if(data_get($customConfig, 'no_weekends') === true)
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
                                label: '{{ data_get($date, 'label') }}'
                            });
                        }
                    }
                }
            );
        </script>
    @endpush

</div>

