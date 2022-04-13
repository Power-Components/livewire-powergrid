@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
@props([
'primaryKey' => null,
'row' => null,
'field' => null,
'theme' => null,
'currentTable' => null,
'tableName' => null,
'options' => []
])
<div x-cloak
     x-data='pgEditable({
       tableName: "{{ $tableName }}",
       id: "{{ $row->{$primaryKey} }}",
       dataField: "{{ $field }}",
       content: "{{ $helperClass->resolveContent($currentTable, $field, $row) }}",
       options: {!! json_encode($options) !!}
             })'>
    <div>
        <select
                class="{{ $theme->inputClass }} p-2"
                x-on:change="save()"
                x-ref="editable"
                x-model="content">
            @foreach($options as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
</div>
