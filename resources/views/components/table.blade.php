@include('livewire-powergrid::components.frameworks.tailwind.header')

@if(config('livewire-powergrid.filter') === 'outside')
    @if(count($make_filters) > 0)
        <div>
{{--            @include('livewire-powergrid::tailwind.2.filter')--}}
        </div>
    @endif
@endif

@include('livewire-powergrid::components.frameworks.tailwind.message')

<x-livewire-powergrid::table-base :theme="$theme->table">
    <x-slot name="header">
        <tr class="{{ $theme->table->trClass }}" style="{{ $theme->table->trStyle }}">
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
                    style="{{ $theme->table->thStyle }}"
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
            <tr class="{{ $theme->table->trBodyClass }}" style="{{ $theme->table->trBodyStyle }}">
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
                    <tr class="{{ $theme->table->trBodyClass }}" style="{{ $theme->table->trBodyClass }}"
                        wire:key="{{ $row->id }}">
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

</x-livewire-powergrid::table-base>


