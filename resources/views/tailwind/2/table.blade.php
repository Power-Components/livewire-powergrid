<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8">

            @include('livewire-powergrid::tailwind.2.export')

            @include('livewire-powergrid::tailwind.2.search-per-page')
            @if(config('livewire-powergrid.filter') === 'outside')
                @if(count($make_filters) > 0)
                    <div>
                        @include('livewire-powergrid::tailwind.2.filter')
                    </div>
                @endif
            @endif

            @include('livewire-powergrid::tailwind.2.loading')
            <div
                class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        @include('livewire-powergrid::tailwind.2.checkbox-all')
                        @foreach($columns as $column)
                            @if($column->hidden === false)

                                <th
                                    class="@if(isset($column->sortable)) pl-0 align-middle cursor-pointer hover:text-black hover:text-current @endif
                                        px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="@if($column->sortable === true) cursor:pointer; @endif {{(isset($column->header_style)? $column->header_style: "")}}"
                                >
                                    <div class="align-content-between" style="display: flex; align-items: center; justify-content: left;">
                                        @if($column->sortable === true)
                                            <span class="text-base pr-2">
                                                @if ($orderBy !== $column->field)
                                                    {!! $sortIcon !!}
                                                @elseif ($orderAsc)
                                                    {!! $sortAscIcon !!}
                                                @else
                                                    {!! $sortDescIcon !!}
                                                @endif
                                            </span>
                                        @endif
                                        <span
                                            @if($column->sortable === true) wire:click="setOrder('{{$column->field}}')" @endif>
                                        {{$column->title}}
                                        </span>
                                        @if(count($filters))
                                            @if(\Illuminate\Support\Arr::exists($column->inputs, 'date_picker') || \Illuminate\Support\Arr::exists($column->inputs, 'select'))
                                                <span
                                                    title="{{ trans('livewire-powergrid::datatable.labels.clear_filter') }}"
                                                    wire:click.prevent="clearFilter()"
                                                    class="float-right text-red-800 cursor-pointer">
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
                            <th scope="col" colspan="{{count($actionBtns)}}"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ trans('livewire-powergrid::datatable.labels.action') }}
                            </th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="text-gray-800">
                    @include('livewire-powergrid::tailwind.2.inline-filter')
                    @if(count($data) === 0)
                        <tr class="border-b border-gray-200 hover:bg-gray-100 ">
                            <td class="text-center p-2" colspan="{{ (($checkbox) ? 1:0)
                        + ((isset($actionBtns)) ? 1: 0)
                        + (count($columns))
                        }}">
                                <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>
                            </td>
                        </tr>
                    @endif
                    @foreach($data as $row)
                        <tr class="border-b border-gray-200 hover:bg-gray-100" wire:key="{{ $row->id }}">
                            @include('livewire-powergrid::tailwind.2.checkbox-row')
                            @foreach($columns as $column)
                                @php
                                    $field = $column->field;
                                @endphp
                                @if($column->hidden === false)
                                    <td class="{{ ($column->body_class != '') ?? "px-6 py-4 whitespace-nowrap" }}"
                                        style="{{ ($column->body_style != '') ?? "" }}"
                                    >
                                        @if($column->editable === true)
                                            <div
                                                x-data="{ value: '<span style=\'border-bottom: dotted 1px;\'>{{ addslashes($row->$field) }}</span>' }">
                                                <button
                                                    x-on:click="value = returnValue({{ $row->id }}, '{{ addslashes($row->$field)  }}', '{{ $field }}');"
                                                    x-html="value"
                                                ></button>
                                            </div>
                                        @else
                                            {{ $row->$field }}
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                            @include('livewire-powergrid::tailwind.2.actions')
                        </tr>
                        <tr class="child_{{ $row->id }} hidden">
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(!is_array($data))
                    <div class="">
                        @if(method_exists($data, 'links'))
                            {!! $data->links('livewire-powergrid::tailwind.2.pagination') !!}
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
