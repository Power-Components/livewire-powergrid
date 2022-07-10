@inject('actionRulesClass','PowerComponents\LivewirePowerGrid\Helpers\ActionRules')

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
            :setUp="$setUp"
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
            @includeWhen($headerTotalColumn, 'livewire-powergrid::components.table-header')

            @foreach($data as $row)
                @php
                    $class            = $theme->table->trBodyClass;
                    $rules            = $actionRulesClass->recoverFromAction('pg:rows', $row);

                    $ruleSetAttribute = data_get($rules, 'setAttribute');

                    if (filled($ruleSetAttribute)) {
                        foreach ($ruleSetAttribute as $attribute) {
                           if (isset($attribute['attribute'])) {
                              $class .= ' '.$attribute['value'];
                           }
                        }
                    }
                @endphp

                @if(isset($setUp['detail']))
                <tbody class="{{ $class }}"
                       x-data="{ detailState: @entangle('setUp.detail.state.'.$row->{$primaryKey}) }"
                       wire:key="{{ md5($row->{$primaryKey} ?? $loop->index) }}"
                >
                @else
                    <tr style="{{ $theme->table->trBodyStyle }}"
                        class="{{ $class }}"
                        wire:key="{{ md5($row->{$primaryKey} ?? $loop->index) }}">
                        @endif

                        @php
                            $ruleRows         = $actionRulesClass->recoverFromAction('pg:rows', $row);
                            $ruleDetailView   = data_get($ruleRows, 'detailView');
                        @endphp

                        @includeWhen(data_get($setUp, 'detail.showCollapseIcon'), powerGridThemeRoot().'.toggle-detail', [
                            'theme' => $theme->table,
                            'view' => data_get($setUp, 'detail.viewIcon') ?? null
                        ])

                        @if($checkbox)
                            @php
                                $rules            = $actionRulesClass->recoverFromAction('pg:checkbox', $row);
                                $ruleHide         = data_get($rules, 'hide');
                                $ruleDisable      = data_get($rules, 'disable');
                                $ruleSetAttribute = data_get($rules, 'setAttribute')[0] ?? [];
                            @endphp
                            @include('livewire-powergrid::components.checkbox-row', [
                                'attribute' => $row->{$checkboxAttribute}
                            ])
                        @endif

                        @include('livewire-powergrid::components.row')

                        <x-livewire-powergrid::actions
                            :primary-key="$primaryKey"
                            :tableName="$tableName"
                            :theme="$theme"
                            :row="$row"
                            :actions="$actions"/>
                    </tr>
                    @if(isset($setUp['detail']))
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
                    @endif
                    @if(isset($setUp['detail']))
                </tbody>
                @endif
            @endforeach

            @includeWhen($footerTotalColumn, 'livewire-powergrid::components.table-footer')
        @endif
    </x-slot>
</x-livewire-powergrid::table-base>
