<div class="flex flex-col">

    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8">

            <button
                class="mb-1 bg-indigo-400 text-white focus:bg-indigo-200 focus:outline-none text-sm py-2.5 px-5 rounded-lg items-center inline-flex"
                wire:click="exportToExcel()"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-arrow-down-circle" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"></path>
                </svg>
                <span style="padding-left: 6px">
                   {!! (count($checkbox_values)) ? trans('livewire-powergrid::datatable.buttons.export_selected') :
                    trans('livewire-powergrid::datatable.buttons.export') !!}
                </span>
            </button>

            @include('livewire-powergrid::tailwind.2.search-per-page')

            @if(config('livewire-powergrid.filter') === 'outside')
                @if(count($make_filters) > 0)
                    <div>
                        @include('livewire-powergrid::tailwind.2.filter')
                    </div>
                @endif
            @endif

            <div class="relative pb-2 h-8">
                <span class="inset-y-0 right-4 flex items-center pl-2 pr-2 pt-1 hidden icon_success">
                    <svg class="text-green-500 fill-current w-6 h-6"
                         xmlns="http://www.w3.org/2000/svg" fill="none"
                         stroke="currentColor" stroke-linecap="round"
                         stroke-linejoin="round" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/></svg>
                </span>
                <span class="inset-y-0 right-4 flex items-center pl-2 pr-2 pt-1 hidden icon_error">
                    <svg class="text-red-500 fill-current w-6 h-6"
                         xmlns="http://www.w3.org/2000/svg" fill="none"
                         stroke="currentColor" stroke-linecap="round"
                         stroke-linejoin="round" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path d="M10 8.586L2.929 1.515 1.515 2.929 8.586 10l-7.071 7.071 1.414 1.414L10 11.414l7.071 7.071 1.414-1.414L11.414 10l7.071-7.071-1.414-1.414L10 8.586z"/></svg>
                </span>
                <span class="inset-y-0 right-4 flex items-center pl-2 pr-2 pt-2 hidden loading">
                    <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-6 w-6"></div>
                </span>
            </div>
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>

                        @include('livewire-powergrid::tailwind.2.checkbox-all')

                        @foreach($columns as $column)
                            @if($column->hidden === false)
                                <th @if($column->sortable === true) wire:click="setOrder('{{$column->field}}')"
                                    @endif
                                    class="@if(isset($column->sortable)) align-middle cursor-pointer hover:text-black hover:text-current @endif
                                        px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="@if($column->sortable === true) cursor:pointer; @endif {{(isset($column->header_style)? $column->header_style: "")}}"
                                >
                                    @if($column->sortable === true)
                                        <svg style="display: inline-block;" xmlns="http://www.w3.org/2000/svg"
                                             width="16"
                                             height="16" fill="currentColor" class="bi bi-sort-up"
                                             viewBox="0 0 16 16">
                                            <path d="{{$icon_sort[$column->field]}}"/>
                                        </svg>
                                    @endif
                                    {{$column->title}}
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
                                <span>Nenhum registro encontrado</span>
                                <span wire:click.prevent="clearFilter()" style="font-weight: bold; cursor: pointer">
                                        Limpar filtro
                                </span>
                            </td>
                        </tr>
                    @endif

                    @foreach($data as $row)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">

                            @include('livewire-powergrid::tailwind.2.checkbox-row')

                            @foreach($columns as $column)

                                @php
                                    $field = $column->field;
                                @endphp

                                @if($column->hidden === false)
                                    <td class="{{(isset($column->body_class)? $column->body_class: "px-6 py-4 whitespace-nowrap")}}"
                                        style="{{(isset($column->body_style)? $column->body_style: "")}}"
                                    >
                                        @if($column->editable === true && $perPage == 0)
                                            <div
                                                x-on:click="input=true"
                                                x-data="{ value: '<span style=\'border-bottom: dotted 1px;\'>{{ $row->$field }}</span>' }">
                                                <button
                                                    x-on:click="value = returnValue({!! $row->id !!}, '{!! $row->$field !!}', '{!! $field !!}');"
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

