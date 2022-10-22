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
    $fallback = html_entity_decode(data_get($editable, 'fallback'), ENT_QUOTES, 'utf-8');
    $content  = html_entity_decode($helperClass->resolveContent($currentTable, $field, $row), ENT_QUOTES, 'utf-8') ?: $fallback;

    $params = [
        'theme' => $theme->name,
        'tableName' => $tableName,
        'id' => $row->{$primaryKey},
        'dataField' => $field,
        'content' => addslashes($content),
        'fallback' => $fallback,
    ];
@endphp
<div x-cloak
     style="width: 100% !important; height: 100% !important;"
     x-data="pgEditable(@js($params))">
    <div style="border-bottom: dotted 1px; cursor: pointer; width: 100%; height: 100%;"
         x-bind:class="{
            'py-2 px-3' : theme == 'tailwind',
            'p-1' : theme == 'bootstrap5',
         }"
         x-show="!editable"
         x-on:click="editable = true;"
    >
        {{ $content }}
    </div>
    <div x-show="editable" style="margin-bottom: 4px">
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
