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
    data_get(collect($row->__powergrid_rules)->last(), 'toggleDetailView'),
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
        $field = data_get($column, 'field');
        $content = $row->{$field} ?? '';
        $templateContent = null;

        if (is_array($content)) {
            $template = data_get($column, 'template');
            $templateContent = $content;
            $content = '';
        }

        $contentClassField = data_get($column, 'contentClassField');
        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content ?? '');
        $field = data_get($column, 'dataField', data_get($column, 'field'));

        $contentClass = data_get($column, 'contentClasses');

        if (is_array(data_get($column, 'contentClasses'))) {
            $contentClass = array_key_exists($content, data_get($column, 'contentClasses'))
                ? data_get($column, 'contentClasses')[$content]
                : '';
        }
    @endphp
    <td
        @class([
            data_get($theme, 'table.tdBodyClass'),
            data_get($column, 'bodyClass'),
        ])
        @style([
            'display:none' => data_get($column, 'hidden'),
            data_get($column, 'bodyStyle'),
        ])
        wire:key="row-{{ data_get($row, $this->realPrimaryKey) }}-{{ $childIndex ?? 0 }}"
    >
        @if (empty(data_get($row, 'actions')) && data_get($column, 'isAction'))
            <div class="pg-actions">
                @if (method_exists($this, 'actionsFromView') && ($actionsFromView = $this->actionsFromView($row)))
                    <div wire:key="actions-view-{{ data_get($row, $this->realPrimaryKey) }}">
                        {!! $actionsFromView !!}
                    </div>
                @endif

                @if (data_get($column, 'isAction'))
                    <div x-data="pgRenderActions({ rowId: @js(data_get($row, $this->realPrimaryKey)), parentId: @js($parentId) })">
                        <span
                            class="pg-actions-row"
                            x-html="toHtml"
                        ></span>
                    </div>
                @endif
            </div>
        @endif

        @php
            $showEditOnClick = once(fn() => $this->shouldShowEditOnClick($column, $row));
        @endphp

        @if ($showEditOnClick === true)
            <span @class([$contentClassField, $contentClass])>
                @include(data_get($theme, 'editable.view') ?? null, [
                    'editable' => data_get($column, 'editable'),
                ])
            </span>
        @elseif(count(data_get($column, 'toggleable')) > 0)
            @php
                $showToggleable = once(fn() => $this->shouldShowToggleable($column, $row));
            @endphp
            @includeWhen($showToggleable, data_get($theme, 'toggleable.view'), ['tableName' => $tableName])
        @else
            <span @class([$contentClassField, $contentClass])>
                @if (filled($templateContent))
                    <div
                        x-data="pgRenderRowTemplate({
                            rowId: @js(data_get($row, $this->realPrimaryKey)),
                            parentId: @js($parentId),
                            field: @js($field),
                            templateContent: @js($templateContent)
                        })"
                        x-html="rendered"
                    >
                    </div>
                @else
                    <div>{!! data_get($column, 'index') ? $rowIndex : $content !!}</div>
                @endif
            </span>
        @endif
    </td>
@endforeach
