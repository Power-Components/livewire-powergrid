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
                        x-data
                        type="checkbox"
                        {{ $inputAttributes }}
                        x-on:click="$store.pgBulkActions.add($event.target.value, '{{ $tableName }}')"
                        wire:model="checkboxValues"
                        value="{{ $checkboxAttribute }}"
                    >
                </label>
            </div>
        </td>
    @endif
@endif

{{-- Cells rendered as a single PHP-built string (PowerGridComponent::renderCells)
     to bypass the per-cell Blade loop. --}}
{!! $__partial->renderCells($row, $rowIndex, $childIndex, $parentId, $rowId) !!}
