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

        @include('livewire-powergrid::bootstrap.50.alert.message')

        <div class="table-responsive col-md-12" style="margin: 10px 0 10px;">

            <table id="table"
                   class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2">
                <thead>
                <tr>

                    @include('livewire-powergrid::bootstrap.50.checkbox-all')

                    @foreach($columns as $column)

                        @if($column->hidden === false)
                            <th class="{{ $column->header_class ?? '' }}" style="@if($column->sortable === true)cursor:pointer;@endif min-width: 50px;padding-left: 15px;
                                    text-transform: uppercase;
                                    font-size: 0.75rem;
                                    color: #6b6a6a;
                                    padding-top: 8px;
                                    padding-bottom: 8px;{{ $column->header_style ?? '' }}">
                                <div>
                                    @if($column->sortable === true)
                                        <span class="text-base">
                                            @if ($sortField !== $column->field)
                                                {!! $sortIcon !!}
                                            @elseif ($sortDirection)
                                                {!! $sortAscIcon !!}
                                            @else
                                                {!! $sortDescIcon !!}
                                            @endif
                                            </span>
                                    @endif

                                    <span @if($column->sortable === true) wire:click="sortBy('{{$column->field}}')"
                                            @endif>
                                           {{$column->title}}
                                    </span>

                                    @include('livewire-powergrid::bootstrap.50.clear_filter')
                                </div>
                            </th>
                        @endif

                    @endforeach
                    @if(isset($actions) && count($actions))
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
                        <td class="text-center" colspan="{{ (($checkbox) ? 1:0) + ((isset($actions)) ? 1: 0) + (count($columns)) }}">
                            <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>
                        </td>
                    </tr>
                @endif

                @foreach($data as $row)
                    <tr class="border-b border-gray-200 hover:bg-gray-100 " wire:key="{{ $row->id }}">
                        @include('livewire-powergrid::bootstrap.50.checkbox-row')
                        @include('livewire-powergrid::bootstrap.50.rows')
                        @include('livewire-powergrid::bootstrap.50.actions')
                    </tr>
                @endforeach

                </tbody>
            </table>

            @include('livewire-powergrid::bootstrap.50.footer')

        </div>
    </div>

</div>


