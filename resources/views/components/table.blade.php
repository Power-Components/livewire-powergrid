<x-livewire-powergrid::table-base
    :theme="$theme->table">
    <x-slot name="header">
        <tr class="{{ $theme->table->trClass }}"
            style="{{ $theme->table->trStyle }}">
            @if($checkbox)
                <x-livewire-powergrid::checkbox-all
                    :checkbox="$checkbox"
                    :theme="$theme->checkbox"/>
            @endif
            @foreach($columns as $column)
                <x-livewire-powergrid::cols
                    :column="$column"
                    :theme="$theme"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                    :enabledFilters="$enabledFilters"/>
            @endforeach

            @if(isset($actions) && count($actions))
                <th class="{{ $theme->table->thClass .' '. $column->headerClass }}" scope="col"
                    style="{{ $theme->table->thStyle }}"
                    colspan="{{count($actions)}}">
                    {{ trans('livewire-powergrid::datatable.labels.action') }}
                </th>
            @endif
        </tr>
    </x-slot>

    <x-slot name="rows">
        <x-livewire-powergrid::inline-filters
            :makeFilters="$makeFilters"
            :checkbox="$checkbox"
            :actions="$actions"
            :columns="$columns"
            :theme="$theme"
            :enabledFilters="$enabledFilters"
        />
        @if(is_null($data) || count($data) === 0)
            <th>
                <tr class="{{ $theme->table->trBodyClass }}" style="{{ $theme->table->trBodyStyle }}">
                    <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}" colspan="{{ (($checkbox) ? 1:0)
                                    + ((isset($actions)) ? 1: 0)
                                    + (count($columns))
                                    }}">
                        <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>
                    </td>
                </tr>
            </th>
        @else
            @foreach($data as $row)
                <tr class="{{ $theme->table->trBodyClass }}"
                    style="{{ $theme->table->trBodyStyle }}"
                    wire:key="{{ $row->{$primaryKey} ?? $loop->index }}">
                    @if($checkbox)
                        <x-livewire-powergrid::checkbox-row
                            :theme="$theme->checkbox"
                            :attribute="$row->{$checkboxAttribute}"
                            :checkbox="$checkbox"/>
                    @endif

                    <x-livewire-powergrid::row
                        :currentTable="$currentTable"
                        :primaryKey="$primaryKey"
                        :theme="$theme"
                        :row="$row"
                        :columns="$columns"/>

                    <x-livewire-powergrid::actions
                        :theme="$theme"
                        :row="$row"
                        :actions="$actions"/>
                </tr>
            @endforeach
        @endif
    </x-slot>
</x-livewire-powergrid::table-base>
