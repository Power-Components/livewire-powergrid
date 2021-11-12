@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
    'column' => null
])
@if(filled($multiSelect))
    <div wire:ignore class="flex @if(!$inline) col-md-6 col-lg-3 @endif" style="max-width: 370px !important;">

        @if(!$inline)
            <label for="input_{{ data_get($multiSelect, 'data_field') }}">
                {{ data_get($multiSelect, 'label') }}
            </label>
        @endif
        <select data-none-selected-text="{{ trans('livewire-powergrid::datatable.multi_select.select') }}"
                multiple
                id="input_{{ data_get($multiSelect, 'data_field') }}"
                class="power_grid_select select_picker_{{ data_get($multiSelect, 'data_field') }} form-control active"
                wire:ignore
                data-live-search="{{ data_get($multiSelect, 'live-search') }}">

            <option value="">{{ trans('livewire-powergrid::datatable.multi_select.all') }}</option>
            @foreach(data_get($multiSelect, 'data_source') as $relation)
                <option value="{{ data_get($relation, 'id') }}">
                    {{ $relation[data_get($multiSelect, 'display_field')] }}
                </option>
            @endforeach
        </select>
    </div>
    @push('power_grid_scripts')
        <script>
            $(function () {
                $('.select_picker_{{ data_get($multiSelect, 'data_field') }}').selectpicker();
            })

            document.addEventListener('DOMContentLoaded', () => {
                Livewire.hook('message.processed', (message, component) => {
                    $('.select_picker_{{ data_get($multiSelect, 'data_field') }}').selectpicker()
                })
            })

            $('.select_picker_{{ data_get($multiSelect, 'data_field') }}').selectpicker();
            $('select.select_picker_{{ data_get($multiSelect, 'data_field') }}').on('change', function () {
                const selected = $(this).find("option:selected");
                const arrSelected = [];
                selected.each(function () {
                    arrSelected.push($(this).val());
                });
                window.livewire.emit('eventMultiSelect', {
                    id: '{{ data_get($multiSelect, 'data_field') }}',
                    values: arrSelected
                })
                $('.select_picker_{{ data_get($multiSelect, 'data_field') }}').selectpicker('refresh');
            });
        </script>
    @endpush
@endif
