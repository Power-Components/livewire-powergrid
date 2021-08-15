<div x-data="toggle()" x-cloak
     @click.away="open = false">
    <button @click.prevent="open = ! open"
            class="block bg-white-200 text-gray-700 border border-gray-300 rounded py-1.5 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-600 dark:border-gray-500 dark:bg-gray-500 2xl:dark:placeholder-gray-300 dark:text-gray-200 dark:text-gray-300">
        <div class="flex">
            <x-livewire-powergrid::icons.download class="h-6 w-6 text-gray-500 dark:text-gray-300"/>
        </div>
    </button>

    <div x-show="open"
         x-transition:enter="transform duration-200"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transform duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="mt-2 w-48 bg-white shadow-xl absolute z-10 dark:bg-gray-500">

        @if(in_array('excel',$exportType))
            <a wire:click="exportToXLS()" href="#"
               class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-black-200 dark:text-gray-200  dark:hover:bg-gray-700">Excel</a>
        @endif
        @if(in_array('csv',$exportType))
            <a wire:click="exportToCsv()" href="#"
               class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-black-200 dark:text-gray-200 dark:hover:bg-gray-700">Csv</a>
        @endif
    </div>
</div>
<script>
    function toggle() {
        return {
            open: false,
        }
    }
</script>
