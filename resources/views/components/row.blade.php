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
    $ruleDetailView = data_get($rulesValues, 'detailView');
@endphp

@includeWhen(data_get($setUp, 'detail.showCollapseIcon'), powerGridThemeRoot() . '.toggle-detail', [
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
        $content = $row->{$column->field} ?? null;
        $contentClassField = $column->contentClassField != '' ? $row->{$column->contentClassField} : '';
        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
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

        @if (data_get($column->editable, 'hasPermission') && !str_contains($field, '.'))
            <span @class([$contentClassField, $contentClass])>
                @include(data_get($theme, 'editable.view') ?? null, ['editable' => $column->editable])
            </span>
        @elseif(count($column->toggleable) > 0)
            @php
                $rules = $actionRulesClass->recoverFromAction($row, 'pg:rows');
                $toggleableRules = collect(data_get($rules, 'showHideToggleable', []));
                $showToggleable = $toggleableRules->isEmpty() || $toggleableRules->last() == 'show';
            @endphp
            @include(data_get($theme, 'toggleable.view'), ['tableName' => $tableName])
        @else
            <span @class([$contentClassField, $contentClass])>
                <div>{!! $column->index ? $rowIndex : $content !!}</div>
            </span>
        @endif
    </td>
@endforeach
