@props([
    '__partial' => null,
])

@php
    $__partial = $__partial ?? $this;
@endphp

<tr
    wire:key="th-empty-{{ $__partial->tableName }}"
    class="{{ theme('table.layout.tr') }}"
>
    <th
        class="{{ theme('table.body.td.empty_state') }}"
        colspan="999"
    >
        {!! $__partial->processNoDataLabel !!}
    </th>
</tr>
