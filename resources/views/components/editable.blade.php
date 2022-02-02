@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
@props([
    'primaryKey' => null,
    'row' => null,
    'field' => null,
    'theme' => null,
    'currentTable' => null,
    'tableName' => null,
])
<div x-cloak
     x-data="pgEditable({
       tableName: '{{ $tableName }}',
       id: '{{ $row->{$primaryKey} }}',
       dataField: '{{ $field }}',
       content: '{{ $helperClass->resolveContent($currentTable, $field, $row) }}'
     })">
    <div x-html="content"
         style="border-bottom: dotted 1px; cursor: pointer"
         x-show="!editable"
         x-on:click="editable = true"
    ></div>
    <div x-show="editable">
        <input
            type="text"
            x-on:keydown.enter="save()"
            :class="{'cursor-pointer': !editable}"
            class="{{ $theme->inputClass }} p-2"
            x-ref="editable"
            x-text="content"
            :value="$root.firstElementChild.innerText">
    </div>
</div>
