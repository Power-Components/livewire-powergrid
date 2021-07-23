@props([
    'columns' => null
])
<style>
    [x-cloak] {
        display: none;
    }
</style>
<div x-data="toggleColumns()" x-cloak
     {{ $attributes }}
     @click.away="open = false">
    <button @click.prevent="open = ! open"
            class="block w-full bg-white-200 text-gray-700 border border-gray-300 rounded py-1.5 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-600 dark:border-gray-500 dark:bg-gray-500 2xl:dark:placeholder-gray-300 dark:text-gray-200 dark:text-gray-300">
        <div class="flex">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>
        </div>
    </button>

    <div x-show="open"
         x-transition:enter="transform duration-200"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transform duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="mt-2 py-2 w-48 bg-white shadow-xl absolute z-10 dark:bg-gray-500">

        @foreach($columns as $column)
            <div @click="window.livewire.emit('eventToggleColumn', '{{ $column->field }}')"
                class="@if($column->hidden) opacity-40 @endif cursor-pointer flex justify-start block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-black-200 dark:text-gray-200 dark:hover:bg-gray-700">
                @if($column->hidden === false)
                    <x-livewire-powergrid::icons.eye/>
                @else
                    <x-livewire-powergrid::icons.eye-off/>
                @endif

                <div class="ml-2"> {{ $column->title }}</div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function toggleColumns() {
        return {
            open: false,
            toggleColumn(key) {
                window.livewire.emit('eventToggleColumn', key);
            }
        }
    }
</script>
