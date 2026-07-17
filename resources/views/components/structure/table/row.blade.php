@props([
    'rowIndex' => 0,
    'childIndex' => null,
    'parentId' => null,
    '__partial' => null,
])

@php
    $__partial = $__partial ?? $this;
@endphp

@includeWhen(isset($__partial->setUp['responsive']), theme_view('toggle-detail-responsive'), [
    'view' => data_get($__partial->setUp, 'detail.viewIcon') ?? null,
])

@php
    $defaultCollapseIcon = theme_view('toggle-detail');
@endphp

@includeWhen(data_get($__partial->setUp, 'detail.showCollapseIcon'),
    data_get(collect($row->__powergrid_rules)->last(), 'toggleDetailView') ?? $defaultCollapseIcon,
    [
        'setUp' => $__partial->setUp,
        'view' => data_get($__partial->setUp, 'detail.viewIcon') ?? null,
    ]
)

@includeWhen($__partial->radio && $__partial->radioAttribute, theme_view('table.radio-row'), [
    'attribute' => $row->{$__partial->radioAttribute} ?? null,
])

@includeWhen($__partial->checkbox && $__partial->checkboxAttribute, theme_view('table.checkbox-row'), [
    'attribute' => $row->{$__partial->checkboxAttribute} ?? null,
])

@foreach ($__partial->visibleColumns as $column)
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

        if ($content instanceof \UnitEnum) {
            $content = $content instanceof \BackedEnum
                ? $content->value
                : $content->name;
        }

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
            data_get($column, 'isAction') ? theme('table.layout.td_actions') : theme('table.layout.td'),
            data_get($column, 'bodyClass'),
        ])
        @style([
            'display:none' => data_get($column, 'hidden'),
            data_get($column, 'bodyStyle'),
        ])
        wire:key="row-{{ $rowId }}-{{ $field }}-{{ $childIndex ?? 0 }}"
        data-column="{{ data_get($column, 'isAction') ? 'actions' : $field }}"
    >
        @if (count(data_get($column, 'customContent')) > 0)
            @include(data_get($column, 'customContent.view'), data_get($column, 'customContent.params'))
        @else
            @if (data_get($column, 'isAction'))
                <div class="pg-actions">
                    @if (method_exists($__partial, 'actionsFromView') && ($actionsFromView = $__partial->actionsFromView($row)))
                        <div wire:key="actions-view-{{ data_get($row, $__partial->realPrimaryKey) }}">
                            {!! $actionsFromView !!}
                        </div>
                    @endif

                    <div wire:replace.self>
                        @if (data_get($column, 'isAction'))
                            <div
                                x-data="pgRenderActions({ rowId: @js(data_get($row, $__partial->realPrimaryKey)), parentId: @js($parentId) })"
                                class="{{ theme('table.layout.body.td.actions_wrapper') }}"
                                x-html="toHtml"
                            >
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @php
                $pluginContent = $__partial->renderColumnContent($column, $row);
            @endphp

            @if ($pluginContent !== null)
                {!! $pluginContent !!}
            @else
                <span @class([$contentClassField, $contentClass])>
                    @if (filled($templateContent))
                        <div
                            x-data="pgRenderRowTemplate({
                                parentId: @js($parentId),
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
        @endif
    </td>
@endforeach
