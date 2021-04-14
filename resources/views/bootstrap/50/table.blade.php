<div>
    <div class="">
        <div class="col-md-12" style="margin: 10px 0 10px;">
            <button class="btn livewire-powergrid" wire:click="exportToExcel()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-file-earmark-excel" viewBox="0 0 16 16">
                    <path
                        d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                    <path
                        d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                @if(count($checkbox_values) == 0)
                    {{ trans('livewire-powergrid::datatable.buttons.export') }}
                @elseif(count($checkbox_values) == 1)
                    {{ trans('livewire-powergrid::datatable.buttons.export_one') }}
                @else
                    {{ trans('livewire-powergrid::datatable.buttons.export_selected') }}
                @endif

            </button>
        </div>

        <div class="col-md-12">
            @include('livewire-powergrid::bootstrap.50.search-per-page')
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
                   class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
                <thead>
                <tr>

                    @include('livewire-powergrid::bootstrap.50.checkbox-all')

                    @foreach($columns as $column)

                        @if($column->hidden === false)
                            <th
                                class="{{ ($column->header_class != '') ?? "" }}"
                                style="@if($column->sortable === true)cursor:pointer;@endif {{( $column->header_style != '') ?? '' }} min-width: 50px;"
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

                                    @if(count($filters))
                                        @if(\Illuminate\Support\Arr::exists($column->inputs, 'date_picker') || \Illuminate\Support\Arr::exists($column->inputs, 'select'))
                                            <span
                                                title="{{ trans('livewire-powergrid::datatable.labels.clear_filter') }}"
                                                wire:click.prevent="clearFilter()"
                                                style="color: #c30707; cursor:pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                     fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                  <path
                                                      d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                  <path
                                                      d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                </svg>
                                            </span>
                                        @endif
                                    @endif
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
                                <td class="{{ ($column->body_class != '') ?? ""}}"
                                    style="{{(isset($column->body_style)? $column->body_style: "")}}"
                                >
                                    @if($column->editable === true)
                                        @include('livewire-powergrid::bootstrap.50.components.editable')
                                    @elseif($column->toggleable === true)
                                        @include('livewire-powergrid::bootstrap.50.components.toggleable')
                                    @else
                                        {!! $row->$field !!}
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
                <nav aria-label="">
                    @if(method_exists($data, 'links'))
                        {!! $data->links() !!}
                    @endif
                </nav>
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


