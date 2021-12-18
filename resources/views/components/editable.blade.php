@inject('strClass', '\Illuminate\Support\Str')
@props([
    'primaryKey' => null,
    'row' => null,
    'field' => null,
    'theme' => null,
    'currentTable' => null,
])
@php
    $table = $currentTable;
    $currentField = $field;

    if (str_contains($currentField, '.')) {
       $data = $strClass::of($field)->explode('.');
       $table = $data->get(0);
       $field = $data->get(1);

       if ($table === $currentTable) {
           $content = addslashes($row->{$field});
       } else {
           $content = addslashes($row->{$table}->{$field});
       }
   } else {
        $content = addslashes($row->{$field});
   }

   $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);

@endphp
<div wire:ignore.self
     x-cloak
     x-data="pgEditable({
       id: '{{ $row->{$primaryKey} ?? $row->id }}',
       dataField: '{{ $currentField }}',
       content: '{{ $content }}'
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
