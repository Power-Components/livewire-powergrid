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

            @include('livewire-powergrid::tailwind.2.alert.message')

            @include('livewire-powergrid::tailwind.2.loading')
            <div
                class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">

                <x-livewire-powergrid::table :theme="$theme->table">
                    <x-slot name="header">
                        <tr class="{{ $theme->table->trClass }}">
                            <x-livewire-powergrid::checkbox-all
                                :checkbox="$checkbox"
                                :theme="$theme"/>

                            @foreach($columns as $column)
                                <x-livewire-powergrid::cols
                                    :column="$column"
                                    :theme="$theme->table"
                                    :sortField="$sortField"
                                    :sortDirection="$sortDirection"
                                    :sortAscIcon="$sortAscIcon"
                                    :sortDescIcon="$sortDescIcon"
                                    :sortIcon="$sortIcon"
                                    :filtersEnabled="$filters_enabled"/>
                            @endforeach

                            @if(isset($actions) && count($actions))
                                <th class="{{ $theme->table->thClass .' '. $column->header_class }}" scope="col"
                                    colspan="{{count($actions)}}">
                                    {{ trans('livewire-powergrid::datatable.labels.action') }}
                                </th>
                            @endif
                        </tr>
                    </x-slot>

                    <x-slot name="rows">
                        <x-livewire-powergrid::inline-filters
                            :makeFilters="$make_filters"
                            :checkbox="$checkbox"
                            :columns="$columns"
                            :theme="$theme"
                        />
                        @if(is_null($data) || count($data) === 0)
                            <tr class="{{ $theme->table->trBodyClass }}">
                                <td class="text-center p-2" colspan="{{ (($checkbox) ? 1:0)
                                    + ((isset($actions)) ? 1: 0)
                                    + (count($columns))
                                    }}">
                                    <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>
                                </td>
                            </tr>
                        @else
                            <th>
                                @foreach($data as $row)
                                    <tr class="{{ $theme->table->trBodyClass }}" wire:key="{{ $row->id }}">
                                        <x-livewire-powergrid::checkbox-row
                                            :theme="$theme"
                                            :attribute="$row->{$checkboxAttribute}"
                                            :checkbox="$checkbox"/>

                                        <x-livewire-powergrid::row
                                            :theme="$theme"
                                            :row="$row"
                                            :columns="$columns"/>

                                        <x-livewire-powergrid::actions
                                            :theme="$theme"
                                            :row="$row"
                                            :actions="$actions"/>
                                    </tr>
                                @endforeach
                            </th>
                        @endif
                    </x-slot>

                </x-livewire-powergrid::table>


                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        @include('livewire-powergrid::tailwind.2.checkbox-all')
                        @foreach($columns as $column)
                            @if($column->hidden === false)
                                <th
                                    class="px-2 pr-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap {{ $column->header_class ?? '' }}"
                                    style="width: max-content;@if($column->sortable)cursor:pointer; @endif {{ $column->header_style ?? '' }}"
                                >
                                    <div class="align-content-between">
                                        @if($column->sortable === true)
                                            <span class="text-base pr-2">
									            @if ($sortField !== $column->field)
                                                    {!! $sortIcon !!}
                                                @elseif ($sortDirection)
                                                    {!! $sortAscIcon !!}
                                                @else
                                                    {!! $sortDescIcon !!}
                                                @endif
									        </span>
                                        @endif
                                        <span
                                            @if($column->sortable === true) wire:click="sortBy('{{$column->field}}')" @endif>
                                            {{$column->title}}
                                        </span>
                                        @include('livewire-powergrid::tailwind.2.clear_filter')
                                    </div>
                                </th>
                            @endif
                        @endforeach
                        @if(isset($actions) && count($actions))
                            <th scope="col" colspan="{{count($actions)}}"
                                class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ trans('livewire-powergrid::datatable.labels.action') }}
                            </th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="text-gray-800">
                    {{--                    @include('livewire-powergrid::tailwind.2.inline-filter')--}}
                    @if(is_null($data) || count($data) === 0)
                        <tr class="border-b border-gray-200 hover:bg-gray-100 ">
                            <td class="text-center p-2" colspan="{{ (($checkbox) ? 1:0)
                                    + ((isset($actions)) ? 1: 0)
                                    + (count($columns))
                                    }}">
                                <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>
                            </td>
                        </tr>
                    @else

                        @foreach($data as $row)
                            <tr class="border-b border-gray-200 hover:bg-gray-100" wire:key="{{ $row->id }}">
                                @include('livewire-powergrid::tailwind.2.checkbox-row')
                                @include('livewire-powergrid::tailwind.2.rows')
                                @include('livewire-powergrid::tailwind.2.actions')
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('livewire-powergrid::tailwind.2.footer')

</div>
