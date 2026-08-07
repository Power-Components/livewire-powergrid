@props([
    'rowIndex' => 0,
    'childIndex' => null,
    'parentId' => null,
    '__partial' => null,
])

@use('PowerComponents\LivewirePowerGrid\Support\RowRenderer')

{!! (new RowRenderer($__partial ?? $this))->render($row, $rowIndex, $childIndex, $parentId, $rowId) !!}
