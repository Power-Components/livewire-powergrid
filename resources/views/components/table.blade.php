@inject('actionRulesClass', 'PowerComponents\LivewirePowerGrid\Components\Rules\RulesController')

<x-livewire-powergrid::table-base
    :theme="$theme->table"
    :ready-to-load="$readyToLoad"
>
    <x-slot:header>
        <tr
            class="{{ $theme->table->trClass }}"
            style="{{ $theme->table->trStyle }}"
        >
            @if (data_get($setUp, 'detail.showCollapseIcon'))
                <th
                    scope="col"
                    class="{{ $theme->table->thClass }}"
                    style="{{ $theme->table->thStyle }}"
                    wire:key="{{ md5('showCollapseIcon') }}"
                >
                </th>
            @endif

            @isset($setUp['responsive'])
                <th
                    fixed
                    x-show="hasHiddenElements"
                    class="{{ $theme->table->thClass }}"
                    style="{{ $theme->table->thStyle }}"
                >
                </th>
            @endisset

            @if ($checkbox)
                <x-livewire-powergrid::checkbox-all
                    :checkbox="$checkbox"
                    :theme="$theme->checkbox"
                />
            @endif

            @foreach ($columns as $column)
                <x-livewire-powergrid::cols
                    wire:key="cols-{{ $column->field }} }}"
                    :column="$column"
                    :theme="$theme"
                    :enabledFilters="$enabledFilters"
                />
            @endforeach

            @if (isset($actions) && count($actions))
                @php
                    $responsiveActionsColumnName = PowerComponents\LivewirePowerGrid\Responsive::ACTIONS_COLUMN_NAME;
                    
                    $isActionFixedOnResponsive = isset($this->setUp['responsive']) && in_array($responsiveActionsColumnName, data_get($this->setUp, 'responsive.fixedColumns')) ? true : false;
                @endphp

                <th
                    @if ($isActionFixedOnResponsive) fixed @endif
                    class="{{ $theme->table->thClass . ' ' . $theme->table->thActionClass }}"
                    scope="col"
                    style="{{ $theme->table->thStyle . ' ' . $theme->table->thActionStyle }}"
                    colspan="{{ count($actions) }}"
                    wire:key="{{ md5('actions') }}"
                >
                    {{ trans('livewire-powergrid::datatable.labels.action') }}
                </th>
            @endif

        </tr>
    </x-slot:header>

    <x-slot:loading>
        <tr
            class="{{ $theme->table->trBodyClass }}"
            style="{{ $theme->table->trBodyStyle }}"
        >
            <td
                class="{{ $theme->table->tdBodyEmptyClass }}"
                colspan="{{ ($checkbox ? 1 : 0) + count($columns) }}"
            >
                @if ($loadingComponent)
                    @include($loadingComponent)
                @else
                    {{ __('Loading') }}
                @endif
            </td>
        </tr>
    </x-slot:loading>

    <x-slot:rows>

        @if ($this->hasColumnFilters)
            <x-livewire-powergrid::inline-filters
                :checkbox="$checkbox"
                :actions="$actions"
                :columns="$columns"
                :theme="$theme"
                :filters="$filters"
                :enabledFilters="$enabledFilters"
                :tableName="$tableName"
                :setUp="$setUp"
            />
        @endif
        @if (is_null($data) || count($data) === 0)
            <th>
                <tr
                    class="{{ $theme->table->trBodyClass }}"
                    style="{{ $theme->table->trBodyStyle }}"
                >
                    <td
                        class="{{ $theme->table->tdBodyEmptyClass }}"
                        style="{{ $theme->table->tdBodyEmptyStyle }}"
                        colspan="{{ ($checkbox ? 1 : 0) + count($columns) + (data_get($setUp, 'detail.showCollapseIcon') ? 1 : 0) }}"
                    >
                        <span>{{ trans('livewire-powergrid::datatable.labels.no_data') }}</span>
                    </td>
                </tr>
            </th>
        @else
            @includeWhen($headerTotalColumn, 'livewire-powergrid::components.table-header')
            @foreach ($data as $row)
                @if(!isset($row->{$checkboxAttribute}))@php throw new Exception('To use checkboxes, you must define a unique key attribute in your data source.') @endphp @endif
                @php
                    $class = $theme->table->trBodyClass;
                    $rules = $actionRulesClass->recoverFromAction($row);
                    $rowId = data_get($row, $primaryKey);
                    
                    $ruleSetAttribute = data_get($rules, 'setAttribute');
                    
                    $applyRulesLoop = true;
                    if (method_exists($this, 'actionRules')) {
                        $applyRulesLoop = $actionRulesClass->loop($this->actionRules($row), $loop);
                    }
                    
                    if (filled($ruleSetAttribute) && $applyRulesLoop) {
                        foreach ($ruleSetAttribute as $attribute) {
                            if (isset($attribute['attribute'])) {
                                $class .= ' ' . $attribute['value'];
                            }
                        }
                    }
                @endphp

                <div wire:key="{{ md5($row->{$primaryKey} ?? $loop->index) }}">
                    @if (isset($setUp['detail']))
                        <tbody
                            class="{{ $class }}"
                            x-data="{ detailState: @entangle('setUp.detail.state.' . $row->{$primaryKey}) }"
                        >
                        @else
                            <tr
                                style="{{ $theme->table->trBodyStyle }}"
                                class="{{ trim($class) }}"
                            >
                    @endif
                </div>

                @includeWhen(isset($setUp['responsive']), powerGridThemeRoot() . '.toggle-detail-responsive', [
                    'theme' => $theme->table,
                    'rowId' => $rowId,
                    'view' => data_get($setUp, 'detail.viewIcon') ?? null,
                ])

                @php
                    $ruleRows = $actionRulesClass->recoverFromAction($row);
                    $ruleDetailView = data_get($ruleRows, 'detailView');
                @endphp

                @includeWhen(data_get($setUp, 'detail.showCollapseIcon'),
                    powerGridThemeRoot() . '.toggle-detail',
                    [
                        'theme' => $theme->table,
                        'view' => data_get($setUp, 'detail.viewIcon') ?? null,
                    ]
                )

                @if ($checkbox)
                    @php
                        $rules = $actionRulesClass->recoverFromAction($row);
                        
                        $ruleHide = data_get($rules, 'hide');
                        $ruleDisable = data_get($rules, 'disable');
                        $ruleSetAttribute = data_get($rules, 'setAttribute')[0] ?? [];
                    @endphp
                    @include('livewire-powergrid::components.checkbox-row', [
                        'attribute' => $row->{$checkboxAttribute},
                    ])
                @endif

                <div wire:key="row-{{ $row->{$primaryKey} }}-{{ uniqid() }}">
                    @include('livewire-powergrid::components.row', ['rowIndex' => $loop->index + 1])
                </div>

                </tr>
                @if (isset($setUp['detail']))
                    <template
                        x-cloak
                        x-if="detailState"
                    >
                        <tr>
                            <td colspan="999">
                                @if (isset($ruleDetailView[0]['detailView']))
                                    @includeWhen(data_get($setUp, 'detail.state.' . $row->{$primaryKey}),
                                        $ruleDetailView[0]['detailView'],
                                        [
                                            'id' => data_get($row, $primaryKey),
                                            'options' => array_merge(
                                                data_get($setUp, 'detail.options'),
                                                $ruleDetailView[0]['options']),
                                        ]
                                    )
                                @else
                                    @includeWhen(data_get($setUp, 'detail.state.' . $row->{$primaryKey}),
                                        data_get($setUp, 'detail.view'),
                                        [
                                            'id' => data_get($row, $primaryKey),
                                            'options' => data_get($setUp, 'detail.options'),
                                        ]
                                    )
                                @endif
                            </td>
                        </tr>
                    </template>
                @endif
                @if (isset($setUp['detail']))
                    </tbody>
                @endif

                @includeWhen(isset($setUp['responsive']), 'livewire-powergrid::components.expand-container')
            @endforeach

            @includeWhen($footerTotalColumn, 'livewire-powergrid::components.table-footer')
        @endif
    </x-slot:rows>
</x-livewire-powergrid::table-base>
