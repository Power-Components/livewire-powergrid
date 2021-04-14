<div
    class="relative"
    x-on:click="input=true"
    x-data="{ value: '<span style=\'border-bottom: dotted 1px;\'>{{ addslashes($row->$field)  }}</span>' }">
    <button
        style="width: 100%;text-align: left;border: 0;padding: 4px;background: none;"
        x-on:click="value = returnValue({{ $row->id }}, '{{ addslashes($row->$field) }}', '{{ $field }}');"
        x-html="value"
    ></button>
</div>
