@if($toggleColumns)
    <div x-data="{open: false}"
         class="mr-0 sm:mr-2 mt-2 sm:mt-0"
         @click.away="open = false">
        <button @click.prevent="open = ! open"
                class="block bg-gray-50 text-gray-700 border border-gray-300 rounded py-1.5 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-600 dark:border-gray-500 dark:bg-gray-500 2xl:dark:placeholder-gray-300 dark:text-gray-200 dark:text-gray-300">
            <div class="flex">
                <x-livewire-powergrid::icons.eye-off class="text-gray-500 dark:text-gray-300"/>
            </div>
        </button>

        <div x-show="open"
             x-cloak
             x-transition:enter="transform duration-200"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transform duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="mt-2 py-2 w-48 bg-white shadow-xl absolute z-10 dark:bg-gray-500">

            @foreach($columns as $column)
                @if($column->forceHidden == false)
                <div wire:click="$emit('pg:toggleColumn-{{ $tableName }}', '{{ $column->field }}')"
                     wire:key="toggle-column-{{ $column->field }}"
                     class="@if($column->hidden) opacity-40 @endif cursor-pointer flex justify-start block px-4 py-2 text-gray-800 hover:bg-gray-50 hover:text-black-200 dark:text-gray-200 dark:hover:bg-gray-700">
                    @if(!$column->hidden)
                        <x-livewire-powergrid::icons.eye class="text-gray-500 dark:text-gray-300"/>
                    @else
                        <x-livewire-powergrid::icons.eye-off class="text-gray-500 dark:text-gray-300"/>
                    @endif
                    <div class="ml-2">
                        {{ $column->title }}
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
@endif
