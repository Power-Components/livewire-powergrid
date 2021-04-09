@if(filled($select))

        <div wire:ignore class="@if(!$inline) col-md-6 col-lg-3 @endif {!! ($select['class'] != '') ?? '' !!} pt-2" style="max-width: 370px !important;">

                    @if(!$inline)
                        <label for="input_{!! $select['relation_id'] !!}">{{$select['label']}}</label>
                    @endif

                    <select id="input_{!! $select['relation_id'] !!}"
                            class="livewire_powergrid_select selectpicker_{!! $select['relation_id'] !!}
                                form-control active
{{ (isset($class) != '') ? $class : '' }}"

                            wire:model="filters.select.{!! $select['relation_id'] !!}"
                            wire:ignore
                            data-live-search="{{ (isset($select['live-search']))? $select['live-search']: false }}">

                        <option value="">{{ __('Todos') }}</option>
                        @foreach($select['data_source'] as $relation)
                            <option value="{{ $relation['id'] }}">{{ $relation[$select['display_field']] }}</option>
                        @endforeach
                    </select>

                </div>

                @push('datatable_scripts')
                    <script>
                        $('.selectpicker_{!! $select['relation_id'] !!}').selectpicker();
                        $('select.selectpicker_{!! $select['relation_id'] !!}').on('change', function () {
                            let data = [];
                            data.push({
                                selected: $('.selectpicker_{!! $select['relation_id'] !!} option:selected').val(),
                                field: "{!! $select['relation_id'] !!}"
                            })
                            $('.spinner').html('<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>')
                            $('.selectpicker_{!! $select['field'] !!}').selectpicker('refresh');

                        });
                    </script>
        @endpush
    @endif
