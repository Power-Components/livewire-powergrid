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
        class="{{ theme('table.layout.body.td.empty_state', 'px-6 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400') }}"
        colspan="999"
    >
        {!! $__partial->processEmptyState !!}
    </th>
</tr>
