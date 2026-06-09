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
        'view' => data_get($__partial->setUp, 'detail.viewIcon') ?? null,
    ]
)

@includeWhen($__partial->radio && $__partial->radioAttribute, theme_view('table.radio-row'), [
    'attribute' => $row->{$__partial->radioAttribute} ?? null,
])

{{-- Checkbox inline (optimized) --}}
@if($__partial->checkbox && $__partial->checkboxAttribute)
    @php
        $checkboxAttribute = $row->{$__partial->checkboxAttribute} ?? null;
        $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
            'class' => theme('table.checkbox.input'),
        ]);

        $rules = collect($row->__powergrid_rules)
            ->where('apply', true)
            ->where('forAction', \PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager::TYPE_CHECKBOX)
            ->last();

        if (isset($rules['attributes'])) {
            foreach ($rules['attributes'] as $key => $value) {
                $inputAttributes = $inputAttributes->merge([$key => $value]);
            }
        }

        $disable = (bool) data_get($rules, 'disable');
        $hide = (bool) data_get($rules, 'hide');
    @endphp

    @if ($hide)
        <td wire:key="checkbox-row-hide-{{ $checkboxAttribute }}" class="{{ theme('table.checkbox.th') }}"></td>
    @elseif($disable)
        <td wire:key="checkbox-row-disable-{{ $checkboxAttribute }}" class="{{ theme('table.checkbox.th') }}">
            <div class="{{ theme('table.checkbox.base') }}">
                <label class="{{ theme('table.checkbox.label') }}">
                    <input {{ $inputAttributes }} disabled type="checkbox">
                </label>
            </div>
        </td>
    @else
        <td wire:key="checkbox-row-{{ $checkboxAttribute }}" class="{{ theme('table.checkbox.th') }}">
            <div class="{{ theme('table.checkbox.base') }}">
                <label class="{{ theme('table.checkbox.label') }}">
                    <input
                        x-data="{}"
                        type="checkbox"
                        {{ $inputAttributes }}
                        x-on:click="window.Alpine.store('pgBulkActions').add($event.target.value, '{{ $tableName }}')"
                        wire:model="checkboxValues"
                        value="{{ $checkboxAttribute }}"
                    >
                </label>
            </div>
        </td>
    @endif
@endif

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
            theme('table.layout.td'),
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
                            <div class="{{ theme('table.body.td.actions_wrapper') }}">
                                {!! $__partial->renderActions($row) !!}
                            </div>
                        @endif
                    </div>
                </div>
            @else
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
        @endif
    </td>
@endforeach
