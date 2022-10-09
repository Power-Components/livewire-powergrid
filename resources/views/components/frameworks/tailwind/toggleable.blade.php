@php
    $showDefaultToggle = false;
    if (str_contains($primaryKey, '.')) {
        $showDefaultToggle = true;
    }
@endphp
<div x-data="pgToggleable({
            id: '{{ $row->{$primaryKey} }}',
            isHidden: {{ !$showToggleable ? 'true' : 'false' }},
            tableName: '{{ $tableName }}',
            field: '{{ $column->field }}',
            toggle: {{ (int)$row->{$column->field} }},
            trueValue: '{{ $column->toggleable['default'][0] }}',
            falseValue:  '{{ $column->toggleable['default'][1] }}',
         })">
    @if($column->toggleable['enabled'] && !$showDefaultToggle && $showToggleable === true)
        <div class="flex justify-center">
            <div class="relative rounded-full w-12 h-6 transition duration-200 ease-linear"
                 :class="[toggle === 1 ? 'bg-blue-400 dark:bg-blue-500' : 'bg-slate-400']">
                <label
                    class="absolute left-0 bg-white border-2 mb-2 w-6 h-6 rounded-full transition transform duration-100 ease-linear cursor-pointer"
                    :class="[toggle === 1 ? 'translate-x-full border-blue-400' : 'translate-x-0 border-slate-400']"></label>
                <input type="checkbox"
                       class="appearance-none w-full h-full active:outline-none focus:outline-none"
                       x-on:click="save">
            </div>
        </div>
    @else
        <div class="flex flex-row justify-center">
            @if($row->{$column->field} == 0)
                <div x-text="falseValue"
                     class="text-xs px-4 w-auto py-1 text-center bg-red-200 text-red-800 rounded-md">
                </div>
            @else
                <div x-text="trueValue"
                     class="text-xs px-4 w-auto py-1 text-center bg-blue-200 text-blue-800 rounded-md">
                </div>
            @endif
        </div>
    @endif
</div>
