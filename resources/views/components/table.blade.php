@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')

<x-livewire-powergrid::table-base :theme="$theme->table">
    <x-slot name="header">
        <tr class="{{ $theme->table->trClass }}" style="{{ $theme->table->trStyle }}">
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
                    style="{{ $theme->table->thStyle }}" colspan="{{ count($actions )}}"
                    wire:key="{{ md5('actions') }}">
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
            :filters="$filters"
            :enabledFilters="$enabledFilters"
            :inputTextOptions="$inputTextOptions"
            :tableName="$tableName"
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
            @if($headerTotalColumn)
                <x-livewire-powergrid::table-header
                    :currentTable="$currentTable"
                    :primaryKey="$primaryKey"
                    :theme="$theme"
                    :columns="$columns"
                    :checkbox="$checkbox"
                    :data="$data"
                    :actions="$actions"
                    :withoutPaginatedData="$withoutPaginatedData"/>
            @endif
            @foreach($data as $row)
                @php
                    $class            = $theme->table->trBodyClass;
                    $rules            = $helperClass->makeActionRules('pg:rows', $row);

                    $ruleSetAttribute = data_get($rules, 'setAttribute');

                    if (filled($ruleSetAttribute)) {
                        foreach ($ruleSetAttribute as $attribute) {
                           if (isset($attribute['attribute'])) {
                              $class .= ' '.$attribute['value'];
                           }
                        }

                    }
                @endphp
                <tr class="{{ $class }}"
                    style="{{ $theme->table->trBodyStyle }}"
                    wire:key="{{ md5($row->{$primaryKey} ?? $loop->index) }}">
                    @if($checkbox)
                        @php
                            $rules            = $helperClass->makeActionRules('pg:checkbox', $row);
                            $ruleHide         = data_get($rules, 'hide');
                            $ruleDisable      = data_get($rules, 'disable');
                            $ruleSetAttribute = data_get($rules, 'setAttribute');
                        @endphp
                        <x-livewire-powergrid::checkbox-row
                            :theme="$theme->checkbox"
                            :ruleHide="$ruleHide"
                            :ruleDisable="$ruleDisable"
                            :ruleSetAttribute="$ruleSetAttribute[0] ?? []"
                            :attribute="$row->{$checkboxAttribute}"
                            :checkbox="$checkbox"/>
                    @endif

                    <x-livewire-powergrid::row
                        :tableName="$tableName"
                        :currentTable="$currentTable"
                        :primaryKey="$primaryKey"
                        :theme="$theme"
                        :row="$row"
                        :columns="$columns"/>

                    <x-livewire-powergrid::actions
                        :primary-key="$primaryKey"
                        :theme="$theme"
                        :row="$row"
                        :actions="$actions"/>
                </tr>
            @endforeach
            @if($footerTotalColumn)
                <x-livewire-powergrid::table-footer
                    :currentTable="$currentTable"
                    :primaryKey="$primaryKey"
                    :theme="$theme"
                    :columns="$columns"
                    :checkbox="$checkbox"
                    :data="$data"
                    :actions="$actions"
                    :withoutPaginatedData="$withoutPaginatedData"/>
            @endif
        @endif
    </x-slot>
</x-livewire-powergrid::table-base>
