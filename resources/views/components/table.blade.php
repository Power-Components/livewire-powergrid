@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')

<x-livewire-powergrid::table-base :theme="$theme->table">
    <x-slot name="header">
        <tr class="{{ $theme->table->trClass }}" style="{{ $theme->table->trStyle }}">
            @if(data_get($setUp, 'detail.showCollapseIcon'))
                <th scope="col" class="{{ $theme->table->thClass }}"
                    style="{{ $theme->table->thStyle }}"
                    wire:key="{{ md5('showCollapseIcon') }}">
                </th>
            @endif

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

                <tbody class="{{ $class }}"
                       x-data="{ detailState: @entangleWhen(isset($setUp['detail']), 'setUp.detail.state.'.$row->{$primaryKey}, false) }"
                >
                    <tr
                        style="{{ $theme->table->trBodyStyle }}"
                        wire:key="{{ md5($row->{$primaryKey} ?? $loop->index) }}">

                        @php
                            $rules            = $helperClass->makeActionRules('pg:checkbox', $row);
                            $ruleRows         = $helperClass->makeActionRules('pg:rows', $row);
                            $ruleHide         = data_get($rules, 'hide');
                            $ruleDisable      = data_get($rules, 'disable');
                            $ruleSetAttribute = data_get($rules, 'setAttribute');
                            $ruleDetailView   = data_get($ruleRows, 'detailView');
                        @endphp

                        @includeWhen(data_get($setUp, 'detail.showCollapseIcon'), powerGridThemeRoot().'.toggle-detail', [
                            'theme' => $theme->table,
                            'view' => data_get($setUp, 'detail.viewIcon') ?? null
                        ])

                        @if($checkbox)
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
                            :showErrorBag="$showErrorBag"
                            :currentTable="$currentTable"
                            :primaryKey="$primaryKey"
                            :theme="$theme"
                            :row="$row"
                            :columns="$columns"/>

                        <x-livewire-powergrid::actions
                            :primary-key="$primaryKey"
                            :tableName="$tableName"
                            :theme="$theme"
                            :row="$row"
                            :actions="$actions"/>
                    </tr>
                    <template x-cloak
                              x-if="detailState">
                        <tr>
                            <td colspan="{{ (($checkbox) ? 1:0)
                                    + ((isset($actions)) ? 1: 0)
                                    + (count($columns))
                                    + (data_get($setUp, 'detail.showCollapseIcon') ? 1: 0)
                                    }}">
                                @if(isset($ruleDetailView[0]['detailView']))
                                    @includeWhen(data_get($setUp, 'detail.state.'.$row->{$primaryKey}), $ruleDetailView[0]['detailView'], [
                                        'id'      => $row->{$primaryKey},
                                        'options' => array_merge(data_get($setUp, 'detail.options'), $ruleDetailView[0]['options'])
                                    ])
                                @else
                                    @includeWhen(data_get($setUp, 'detail.state.'.$row->{$primaryKey}), data_get($setUp, 'detail.view'), [
                                        'id'      => $row->{$primaryKey},
                                        'options' => data_get($setUp, 'detail.options')
                                    ])
                                @endif
                            </td>
                        </tr>
                    </template>
                </tbody>

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
