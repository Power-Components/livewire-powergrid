<div>
    <div class="">
        <div class="col-md-12">
            @include('livewire-powergrid::bootstrap.50.header')
        </div>

        @if(config('livewire-powergrid.filter') === 'outside')
            @if(count($make_filters) > 0)
                <div class="col-md-12">
                    @include('livewire-powergrid::bootstrap.50.filter')
                </div>
            @endif
        @endif

        <div class="message" style="margin: 10px 0 10px;">
            @if (session()->has('success'))
                @include('livewire-powergrid::bootstrap.50.alert.success')
            @elseif (session()->has('error'))
                @include('livewire-powergrid::bootstrap.50.alert.success')
            @endif
        </div>

        <div class="table-responsive col-md-12" style="margin: 10px 0 10px;">

            <table id="table"
                   class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                <thead>
                <tr>

                    @include('livewire-powergrid::bootstrap.50.checkbox-all')

                    @foreach($columns as $column)

                        @if($column->hidden === false)
                            <th
                                class="{{ ($column->header_class != '') ?? "" }}"
                                style="@if($column->sortable === true)cursor:pointer;@endif{{( $column->header_style != '') ?? '' }}min-width: 50px;"
                            >
                                <div>
                                    @if($column->sortable === true)
                                        <span class="text-base">
                                                @if ($orderBy !== $column->field)
                                                {!! $sortIcon !!}
                                            @elseif ($orderAsc)
                                                {!! $sortAscIcon !!}
                                            @else
                                                {!! $sortDescIcon !!}
                                            @endif
                                            </span>
                                    @endif

                                    <span @if($column->sortable === true) wire:click="setOrder('{{$column->field}}')"
                                            @endif>
                                           {{$column->title}}
                                    </span>

                                    @include('livewire-powergrid::bootstrap.50.clear_filter')
                                </div>
                            </th>
                        @endif

                    @endforeach
                    @if(isset($actionBtns) && count($actionBtns))
                        <th class="text-center">
                            {{ trans('livewire-powergrid::datatable.labels.action') }}
                        </th>
                    @endif
                </tr>
                </thead>
                <tbody>

                @include('livewire-powergrid::bootstrap.50.inline-filter')

                @if(count($data) === 0)
                    <tr class="border-b border-gray-200 hover:bg-gray-100 ">
                        <td class="text-center" colspan="{{ (($checkbox) ? 1:0)
                                        + ((isset($actionBtns)) ? 1: 0)
                                        + (count($columns))
                                    }}">
                            <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>
                        </td>
                    </tr>
                @endif

                @foreach($data as $row)

                    <tr class="border-b border-gray-200 hover:bg-gray-100 " wire:key="{{ $row->id }}">

                        @include('livewire-powergrid::bootstrap.50.checkbox-row')

                        @foreach($columns as $column)
                            @php
                                $field = $column->field;
                            @endphp

                            @if($column->hidden === false)
                                <td class="{{ ($column->body_class != '') ? $column->body_class: '' }}"
                                    style="{{ ($column->body_style != '') ? $column->body_style: '' }}"
                                >
                                    @if($column->editable === true)
                                        <span class="d-flex justify-content-between">
                                            <div>
                                                @include('livewire-powergrid::bootstrap.50.components.editable')
                                            </div>
                                            <div>
                                                @if(count($column->click_to_copy) > 0)
                                                    <button style="width: 24px; border: 0; height: 30px; background-repeat: no-repeat;" onclick="copyToClipboard(this)" value="copy" class="img_copy" data-value="{{ $row->$field }}" title="{{ $column->click_to_copy['label'] }}" ></button>
                                                @endif
                                            </div>
                                        </span>
                                    @elseif(count($column->toggleable) > 0)
                                        @include('livewire-powergrid::bootstrap.50.components.toggleable')
                                    @else
                                        <span class="d-flex justify-content-between">
                                            <div>
                                                {!! $row->$field !!}
                                            </div>
                                            <div>
                                                @if(count($column->click_to_copy) > 0)
                                                    <button style="width: 24px; border: 0; height: 30px; background-repeat: no-repeat;" onclick="copyToClipboard(this)" value="copy" class="img_copy" data-value="{{ $row->$field }}" title="{{ $column->click_to_copy['label'] }}" ></button>
                                                @endif
                                            </div>
                                        </span>
                                    @endif
                                </td>

                            @endif

                        @endforeach

                        @include('livewire-powergrid::bootstrap.50.actions')
                    </tr>
                @endforeach

                </tbody>
            </table>

            @if(!is_array($data))
                <div class="d-flex justify-content-between">
                    <div class="d-flex justify-content-center">
                        <div>
                            <label class="col-12 col-sm-6 col-md-6" style="width: 120px;">
                                @if($perPage_input)
                                    <select wire:model="perPage" class="livewire_powergrid_select dropdown bootstrap-select form-control" style="width: 110px;">
                                        @foreach($perPageValues as $value)
                                            <option value="{{$value}}"> @if($value == 0) Todos @else {{ $value }} @endif</option>
                                        @endforeach
                                    </select>
                                @endif
                            </label>
                        </div>
                        <span style="padding-top: 8px;padding-left: 6px;">{{ trans('livewire-powergrid::datatable.labels.results_per_page') }}</span>
                    </div>
                    <div>
                        @if(method_exists($data, 'links'))
                            {!! $data->links() !!}
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>
<div class="spinner" style="position: absolute;
    width: 50px;
    z-index: 99;
    left: 50%;
    top: 50%;">
</div>


