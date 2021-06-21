@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
    'column' => null
])
@if(filled($multiSelect))
    <div wire:ignore class="flex @if(!$inline) col-md-6 col-lg-3 @endif" style="max-width: 370px !important;">

        @if(!$inline)
            <label for="input_{!! $multiSelect['relation_id'] !!}">{{$multiSelect['label']}}</label>
        @endif
        <select data-none-selected-text="{{ trans('livewire-powergrid::datatable.multi_select.select') }}"
                multiple id="input_{!! $multiSelect['relation_id'] !!}"
                class="power_grid power_grid_select select_picker_{!! $multiSelect['relation_id'] !!}
                    form-control active"
                wire:ignore
                data-live-search="{{ $multiSelect['live-search'] }}">

            <option value="">{{ trans('livewire-powergrid::datatable.multi_select.all') }}</option>
            @foreach($multiSelect['data_source'] as $relation)
                <option value="{{ $relation['id'] }}">{{ $relation[$multiSelect['display_field']] }}</option>
            @endforeach
        </select>
    </div>
    <style>
        .dropdown-toggle, .dropdown-item {
            padding-left: 15px;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #454444;
            padding-top: 8px;
            padding-bottom: 8px;
            display: inline-block;
            vertical-align: middle;
            line-height: normal;
        }
        .bootstrap-select {
            padding-left: 0 !important;
        }
    </style>

    @push("power_grid_styles")
        <link rel="stylesheet" href="{{ config('livewire-powergrid.plugins.bootstrap-select.css') }}" crossorigin="anonymous"/>
    @endpush

    @push('power_grid_scripts')

        <script src="{{ config('livewire-powergrid.plugins.bootstrap-select.js') }}" crossorigin="anonymous"></script>

        <!-- Power Grid Multi Select Scripts -->
        <script>
            $(function () {
                $('.select_picker_').selectpicker();
            })

            document.addEventListener('DOMContentLoaded', () => {
                Livewire.hook('message.processed', (message, component) => {
                    $('.select_picker_').selectpicker()
                })
            })

            $('.select_picker_{!! $multiSelect['relation_id'] !!}').selectpicker();
            $('select.select_picker_{!! $multiSelect['relation_id'] !!}').on('change', function () {
                const selected = $(this).find("option:selected");
                const arrSelected = [];
                selected.each(function () {
                    arrSelected.push($(this).val());
                });
                window.livewire.emit('eventMultiSelect', {
                    id: '{!! $multiSelect['relation_id'] !!}',
                    values: arrSelected
                })
                $('.select_picker_{!! $multiSelect['field'] !!}').selectpicker('refresh');
            });
        </script>
        <!-- Power Grid Date Picker Scripts -->
    @endpush
@endif
