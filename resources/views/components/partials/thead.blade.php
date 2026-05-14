@props([
    'tableName' => null,
    '__partial' => null,
    'loading' => false,
])

@php
    $__partial = $__partial ?? $this;
    $tableName = $tableName ?? $__partial->tableName;
@endphp

<thead
    wire:partial="pg-thead-{{ $tableName }}"
    wire:key="thead-{{ $tableName }}"
    class="pg-thead {{ theme('table.layout.thead') }}"
>
    @include(theme_view('table.header'), [
        'loading' => $loading,
        '__partial' => $__partial,
    ])
</thead>
