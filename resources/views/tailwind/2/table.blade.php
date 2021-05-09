<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8">

            @include('livewire-powergrid::tailwind.2.header')

            @if(config('livewire-powergrid.filter') === 'outside')
                @if(count($make_filters) > 0)
                    <div>
                        @include('livewire-powergrid::tailwind.2.filter')
                    </div>
                @endif
            @endif

            <div class="message pt-2">
                @if (session()->has('success'))
                    @include('livewire-powergrid::tailwind.2.alert.success')
                @elseif (session()->has('error'))
                    @include('livewire-powergrid::tailwind.2.alert.error')
                @endif
            </div>

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
                                    class="px-2 pr-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap"
                                    style="width: max-content;@if($column->sortable)cursor:pointer; @endif {{ ($column->header_style != '') ? $column->header_style:'' }}"
                                >
                                    <div class="align-content-between">
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

                                        @include('livewire-powergrid::tailwind.2.clear_filter')

                                    </div>
                                </th>
                            @endif
                        @endforeach

                        @if(isset($actionBtns) && count($actionBtns))
                            <th scope="col" colspan="{{count($actionBtns)}}"
                                class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ trans('livewire-powergrid::datatable.labels.action') }}
                            </th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="text-gray-800">

                    @include('livewire-powergrid::tailwind.2.inline-filter')

{{--                    @if(count($data) === 0)--}}
{{--                        <tr class="border-b border-gray-200 hover:bg-gray-100 ">--}}
{{--                            <td class="text-center p-2" colspan="{{ (($checkbox) ? 1:0)--}}
{{--                        + ((isset($actionBtns)) ? 1: 0)--}}
{{--                        + (count($columns))--}}
{{--                        }}">--}}
{{--                                <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endif--}}

                    @foreach($data as $row)
                        <tr class="border-b border-gray-200 hover:bg-gray-100" wire:key="{{ $row->id }}">

                            @include('livewire-powergrid::tailwind.2.checkbox-row')

                            @foreach($columns as $column)
                                @php
                                    $field = $column->field;
                                @endphp

                                @if($column->hidden === false)
                                    <td class="px-3 py-2 whitespace-nowrap {{ ($column->body_class != '') ? $column->body_class : '' }}"
                                        style=" {{ ($column->body_style != '') ? $column->body_style : '' }}"
                                    >
                                        @if($column->editable === true)
                                            <span class="flex justify-between">
                                                <div>
                                                    @include('livewire-powergrid::tailwind.2.components.editable')
                                                </div>
                                                <div>
                                                    @if(count($column->click_to_copy))
                                                        @if($column->click_to_copy['enabled'])
                                                            <button
                                                                style="width: 24px; height: 30px; background-repeat: no-repeat;"
                                                                onclick="copyToClipboard(this)" value="copy"
                                                                class="img_copy" data-value="{{ $row->$field }}"
                                                                title="{{ $column->click_to_copy['label'] }}"></button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </span>

                                        @elseif(count($column->toggleable) > 0)
                                            @include('livewire-powergrid::tailwind.2.components.toggleable')
                                        @else
                                            <span class="flex justify-between">
                                                <div>
                                                    {!! $row->$field !!}
                                                </div>
                                                <div>
                                                    @if(count($column->click_to_copy))
                                                        @if($column->click_to_copy['enabled'])
                                                            <button
                                                                style="width: 24px; height: 30px; background-repeat: no-repeat;"
                                                                onclick="copyToClipboard(this)" value="copy"
                                                                class="img_copy" data-value="{{ $row->$field }}"
                                                                title="{{ $column->click_to_copy['label'] }}"></button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </span>

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
            </div>
        </div>
    </div>

    <div class="flex flex-row w-full flex justify-between pt-3 bg-white overflow-y-auto relative">
        @if($perPage_input)
            <div class="flex flex-row">
                <div class="relative h-10">
                    <select wire:model="perPage"
                            class="block appearance-none bg-white-200 border border-gray-300 text-gray-700  py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="grid-state">
                        @foreach($perPageValues as $value)
                            <option value="{{$value}}"> @if($value == 0)
                                    {{ trans('livewire-powergrid::datatable.labels.all') }} @else {{ $value }} @endif</option>
                        @endforeach
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20">
                            <path
                                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </div>
                </div>
                <div class="pl-4 hidden sm:block md:block lg:block w-full"
                     style="padding-top: 6px;">{{ trans('livewire-powergrid::datatable.labels.results_per_page') }}</div>
            </div>
        @endif

        @if(!is_array($data))
            <div class="">
                @if(method_exists($data, 'links'))
                    {!! $data->links('livewire-powergrid::tailwind.2.pagination', ['record_count' => $record_count]) !!}
                @endif
            </div>
        @endif
    </div>

</div>

