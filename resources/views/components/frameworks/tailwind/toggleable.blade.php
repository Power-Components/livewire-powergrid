<div>
    @if($column->toggleable['enabled'] === true)
        <div class="items-center"
             x-data="{ toggle_{{ $field }}_{{ $row->id }}: {{ $row->{$field} }} }">
            <div class="relative rounded-full w-12 h-6 transition duration-200 ease-linear"
                 :class="[toggle_{{ $field }}_{{ $row->{$primaryKey} }} === 1 ? 'bg-green-400' : 'bg-gray-400']">
                <label
                    class="absolute left-0 bg-white border-2 mb-2 w-6 h-6 rounded-full transition transform duration-100 ease-linear cursor-pointer"
                    :class="[toggle_{{ $field }}_{{ $row->id }} === 1 ? 'translate-x-full border-green-400' : 'translate-x-0 border-gray-400']"></label>
                <input type="checkbox"
                       class="appearance-none w-full h-full active:outline-none focus:outline-none"
                       @click="saveToggleableInput((toggle_{{ $field }}_{{ $row->{$primaryKey} }} === 0 ? toggle_{{ $field }}_{{ $row->{$primaryKey} }} = 1 : toggle_{{ $field }}_{{ $row->{$primaryKey} }} = 0), {{ $row->{$primaryKey} }}, '{{ $field }}')">
            </div>
            @else
                <div class="flex flex-row">
                    @if($row->{$field} === 0)
                        <div style="padding-top: 0.1em; padding-bottom: 0.1rem"
                             class="text-xs px-3 text-center w-full bg-red-200 text-red-800 rounded-full">
                            {{ $column->toggleable['default'][1] }}
                        </div>
                    @else
                        <div style="padding-top: 0.1em; padding-bottom: 0.1rem"
                             class="text-xs px-3 text-center w-full bg-blue-200 text-blue-800 rounded-full">
                            {{ $column->toggleable['default'][0] }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
        <script>

            function saveToggleableInput(value, id, field) {
                document.getElementsByClassName('message')[0].style.display = "none";
                window.livewire.emit('eventToggleChanged', {
                    id: id,
                    field: field,
                    value: value
                })
            }
        </script>
</div>
