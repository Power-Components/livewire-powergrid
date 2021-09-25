<div>
    @if(data_get($column->toggleable, 'enabled'))
        <div class="flex justify-center"
             x-data="{ toggle: {{ $row->{$field} }} }">
            <div class="relative rounded-full w-12 h-6 transition duration-200 ease-linear"
                 :class="[toggle === 1 ? 'bg-green-400' : 'bg-gray-400']">
                <label
                    class="absolute left-0 bg-white border-2 mb-2 w-6 h-6 rounded-full transition transform duration-100 ease-linear cursor-pointer"
                    :class="[toggle === 1 ? 'translate-x-full border-green-400' : 'translate-x-0 border-gray-400']"></label>
                <input type="checkbox"
                       class="appearance-none w-full h-full active:outline-none focus:outline-none"
                       @click="saveToggleableInput((toggle === 0 ? toggle = 1 : toggle = 0), {{ $row->{$primaryKey} }}, '{{ $field }}')">
            </div>
            @else
                <div class="flex flex-row justify-center">
                    @if($row->{$field} === 0)
                        <div class="text-xs px-4 w-auto py-1 text-center bg-blue-200 text-blue-800 rounded-md">
                            {{ $column->toggleable['default'][1] }}
                        </div>
                    @else
                        <div class="text-xs px-4 w-auto py-1 text-center bg-blue-200 text-blue-800 rounded-md">
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
