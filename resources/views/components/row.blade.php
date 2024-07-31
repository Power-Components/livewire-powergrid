@use('PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager')

@props([
    'rowIndex' => 0,
    'childIndex' => null,
    'parentId' => null,
])

@includeWhen(isset($setUp['responsive']), powerGridThemeRoot() . '.toggle-detail-responsive', [
    'theme' => data_get($theme, 'table'),
    'rowId' => $rowId,
    'view' => data_get($setUp, 'detail.viewIcon') ?? null,
])

@includeWhen(data_get($setUp, 'detail.showCollapseIcon'),
    data_get(collect($this->actionRulesForRows[$rowId])->last(), 'toggleDetailView'),
    [
        'theme' => data_get($theme, 'table'),
        'view' => data_get($setUp, 'detail.viewIcon') ?? null,
    ]
)

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
        @class([data_get($theme, 'table.tdBodyClass'), $column->bodyClass])
        style="{{ $column->hidden === true ? 'display:none' : '' }}; {{ $column->bodyStyle ?? '' }}"
        wire:key="row-{{ data_get($row, $this->realPrimaryKey) }}-{{ $childIndex ?? 0 }}"
    >
        <div class="pg-actions">

            @if (empty(data_get($row, 'actions')) && $column->isAction)
                @if (method_exists($this, 'actionsFromView') && ($actionsFromView = $this->actionsFromView($row)))
                    <div wire:key="actions-view-{{ data_get($row, $this->realPrimaryKey) }}">
                        {!! $actionsFromView !!}
                    </div>
                @endif
            @endif

            @if (data_get($column, 'isAction'))
                <div x-data="pgRenderActions({ rowId: '@js(data_get($row, $this->realPrimaryKey))', parentId: @js($parentId) })">
                    <span
                        class="pg-actions-row"
                        x-html="toHtml"
                    ></span>
                </div>
            @endif
        </div>

        @php
            // =============* Edit On Click *=====================
            $showEditOnClick = false;

            if (data_get($column->editable, 'hasPermission')) {
                $showEditOnClick = true;
            }

            $editOnClickVisibility = data_get(
                collect($this->actionRulesForRows[$rowId])->last(),
                'editOnClickVisibility',
            );

            if ($editOnClickVisibility === 'hide') {
                $showEditOnClick = false;
            }

            if ($editOnClickVisibility === 'show') {
                $showEditOnClick = true;
            }

            $fieldHideEditOnClick = (bool) data_get(
                collect($this->actionRulesForRows[$rowId])->last(),
                'fieldHideEditOnClick',
            );

            if ($fieldHideEditOnClick) {
                $showEditOnClick = false;
            }
        @endphp

        @if ($showEditOnClick === true)
            <span @class([$contentClassField, $contentClass])>
                @include(data_get($theme, 'editable.view') ?? null, ['editable' => $column->editable])
            </span>

            {{-- =============* Toggleable *===================== --}}
        @elseif(count($column->toggleable) > 0)
            @php
                //Default Toggle Permission
                $showToggleable = data_get($column->toggleable, 'enabled', false);

                $toggleableRowRules = data_get(
                    collect($this->actionRulesForRows[$rowId])->last(),
                    'toggleableVisibility',
                );

                // Has permission, but Row Action Rule is changing to hide
                if ($showToggleable && $toggleableRowRules == 'hide') {
                    $showToggleable = false;
                }

                // No permission, but Row Action Rule is forcing to show
                if (!$showToggleable && $toggleableRowRules == 'show') {
                    $showToggleable = true;
                }

                // Particular Rule for this field
                $fieldHideToggleable = (bool) data_get(
                    collect($this->actionRulesForRows[$rowId])->last(),
                    'fieldHideToggleable',
                );

                if ($fieldHideToggleable) {
                    $showToggleable = !$fieldHideToggleable;
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
