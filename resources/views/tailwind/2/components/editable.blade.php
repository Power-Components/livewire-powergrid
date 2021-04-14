<div
    x-data="{ value: '<span style=\'border-bottom: dotted 1px;\'>{{ addslashes($row->$field) }}</span>' }">
    <button
        x-on:click="value = returnValue({{ $row->id }}, '{{ addslashes($row->$field)  }}', '{{ $field }}');"
        x-html="value"
    ></button>
</div>
