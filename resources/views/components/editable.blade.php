@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
@props([
    'primaryKey' => null,
    'row' => null,
    'field' => null,
    'theme' => null,
    'currentTable' => null,
    'tableName' => null,
    'showErrorBag' => null,
    'editable' => null,
])
@php
    $content =  $helperClass->resolveContent($currentTable, $field, $row);
@endphp
<div x-cloak
     style="width: 100% !important; height: 100% !important;"
     x-data="pgEditable({
       theme: '{{ $theme->name }}',
       tableName: '{{ $tableName }}',
       id: '{{ $row->{$primaryKey} }}',
       dataField: '{{ $field }}',
       content: '{{ $content }}',
       fallback: '{{ data_get($editable, 'fallback') }}'
     })">
    <div x-html="content"
         style="border-bottom: dotted 1px; cursor: pointer; width: 100%; height: 100%;"
         x-bind:class="{
            'p-3' : content == '' && theme == 'tailwind',
            'p-4' : content == '' && theme == 'bootstrap5',
         }"
         x-show="!editable"
         x-on:click="editable = true; $refs.editable.focus()"
    ></div>
    <div x-show="editable">
        {{ $input }}
    </div>
    @if($showErrorBag)
        @error($field.".".$row->{$primaryKey})
        <div class="text-sm text-red-800 p-1 transition transition-all duration-200">
            {{ str($message)->replace($field.".".$row->{$primaryKey}, $field) }}
        </div>
        @enderror
    @endif
</div>
