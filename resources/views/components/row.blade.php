@use('PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager')

@props([
    'rowIndex' => 0,
    'childIndex' => null
])

@includeWhen(isset($setUp['responsive']), powerGridThemeRoot() . '.toggle-detail-responsive', [
    'theme' => data_get($theme, 'table'),
    'rowId' => $rowId,
    'view' => data_get($setUp, 'detail.viewIcon') ?? null,
])

@php
    // =============* Get Rules *=====================
    $rowRules = $actionRulesClass->recoverFromAction($row, RuleManager::TYPE_ROWS);

    // =============* Toggle Detail Rules *=====================
    $showToggleDetail = data_get($setUp, 'detail.showCollapseIcon');

    $toggleDetailVisibilityRowRules = collect(data_get($rowRules, 'ToggleDetailVisibility', []));

    if ($toggleDetailVisibilityRowRules) {
        // Has permission, but Row Action Rule is changing to hide
        if ($showToggleDetail && $toggleDetailVisibilityRowRules->last() == 'hide')
        {
            $showToggleDetail = false;
        }

        // No permission, but Row Action Rule is forcing to show
        if (!$showToggleDetail && $toggleDetailVisibilityRowRules->last() == 'show')
        {
            $showToggleDetail = true;
        }
    }

    $toggleDetailView = powerGridThemeRoot() . ($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail');
@endphp

@includeWhen(data_get($setUp, 'detail.showCollapseIcon'), $toggleDetailView, [
    'theme' => data_get($theme, 'table'),
    'view' => data_get($setUp, 'detail.viewIcon') ?? null,
])

@includeWhen($radio, 'livewire-powergrid::components.radio-row', [
    'attribute' => $row->{$radioAttribute},
])

@includeWhen($checkbox, 'livewire-powergrid::components.checkbox-row', [
    'attribute' => $row->{$checkboxAttribute},
])

@foreach ($columns as $column)
    @php
        $content = $row->{$column->field} ?? '';
        $contentClassField = $column->contentClassField != '' ? $row->{$column->contentClassField} : '';
        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content ?? '');
        $field = $column->dataField != '' ? $column->dataField : $column->field;

        $contentClass = $column->contentClasses;

        if (is_array($column->contentClasses)) {
            $contentClass = array_key_exists($content, $column->contentClasses) ? $column->contentClasses[$content] : '';
        }
    @endphp
    <td
        @class([ data_get($theme, 'table.tdBodyClass'), $column->bodyClass])
        style="{{ $column->hidden === true ? 'display:none' : '' }}; {{ data_get($theme, 'table.tdBodyStyle'). ' ' . $column->bodyStyle ?? '' }}"
        wire:key="row-{{ $column->field }}-{{ $childIndex }}"
    >
        <div class="pg-actions">
            @if(empty(data_get($row, 'actions')) && $column->isAction)
                @if (method_exists($this, 'actionsFromView') && $actionsFromView = $this->actionsFromView($row))
                    <div wire:key="actions-view-{{ data_get($row, $primaryKey) }}">
                        {!! $actionsFromView !!}
                    </div>
                @endif
            @endif

            @if (filled(data_get($row, 'actions')) && $column->isAction)
                @foreach (data_get($row, 'actions') as $key => $action)
                    @if(filled($action))
                        <span wire:key="action-{{ data_get($row, $primaryKey) }}-{{ $key }}">
                            {!! $action !!}
                        </span>
                    @endif
                @endforeach
            @endif
        </div>
        @php
        // =============* Get Field Rules *=====================
        $hasFieldRules = $actionRulesClass->recoverActionForField($row, $field);

        // =============* Edit On Click *=====================
        
        $showEditOnClick = false;

        if (data_get($column->editable, 'hasPermission')) {
            $showEditOnClick = true;
        }

        // Check if there is any Role Row for Edit on click
        $editOnClickRowRules = collect(data_get($rowRules, 'EditOnClickVisibility', []));

        if ($editOnClickRowRules) {
            // Has permission, but Row Action Rule is changing to hide
            if ($showEditOnClick && $editOnClickRowRules->last() == 'hide')
            {
                $showEditOnClick = false;
            }

            // No permission, but Row Action Rule is forcing to show
            if (!$showEditOnClick && $editOnClickRowRules->last() == 'show')
            {
                $showEditOnClick = true;
            }
        }

        // Particular Rule for this field
        if (isset($hasFieldRules['field_hide_editonclick'])) {
            $showEditOnClick = !$hasFieldRules['field_hide_editonclick'];
        }

        if (str_contains($field, '.') === true) {
             $showEditOnClick = false;
        }
        @endphp
    
        @if($showEditOnClick === true)
            <span @class([$contentClassField, $contentClass])>
                @include(data_get($theme, 'editable.view') ?? null, ['editable' => $column->editable])
            </span>

        {{-- =============* Toggleable *===================== --}}
        @elseif(count($column->toggleable) > 0)
            @php
                //Default Toggle Permission
                $showToggleable = data_get($column->toggleable, 'enabled', false);

                $toggleableRowRules = collect(data_get($rowRules, 'ToggleableVisibility', []));

                // Has permission, but Row Action Rule is changing to hide
                if ($showToggleable && $toggleableRowRules->last() == 'hide')
                {
                    $showToggleable = false;
                }

                // No permission, but Row Action Rule is forcing to show
                if (!$showToggleable && $toggleableRowRules->last() == 'show')
                {
                    $showToggleable = true;
                }

                // Particular Rule for this field
                if (isset($hasFieldRules['field_hide_toggleable'])) {
                    $showToggleable = !$hasFieldRules['field_hide_toggleable'];
                }

                if (str_contains($field, '.') === true) {
                    $showToggleable = false;
                }

            @endphp
            @include(data_get($theme, 'toggleable.view'), ['tableName' => $tableName])
        @else
            <span @class([$contentClassField, $contentClass])>
                <div>{!! $column->index ? $rowIndex : $content !!}</div>
            </span>
        @endif
    </td>
@endforeach
