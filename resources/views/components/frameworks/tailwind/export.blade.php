<div x-data="{ open: false }"
     @click.away="open = false">
    <button @click.prevent="open = ! open"
            class="block bg-gray-50 text-gray-700 border border-gray-300 rounded py-1.5 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-600 dark:border-gray-500 dark:bg-gray-500 2xl:dark:placeholder-gray-300 dark:text-gray-200 dark:text-gray-300">
        <div class="flex">
            <x-livewire-powergrid::icons.download class="h-6 w-6 text-gray-500 dark:text-gray-300"/>
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
         class="mt-2 w-auto bg-white shadow-xl absolute z-10 dark:bg-gray-500">

        @if(in_array('excel',$exportType))
            <div class="flex px-4 py-2 text-gray-400 dark:text-gray-300">
                <span class="w-12">Excel</span>
                <a x-on:click="$wire.call('exportToXLS'); open = false"
                   href="#"
                   class="px-2 block text-gray-800 hover:bg-gray-50 hover:text-black-300 dark:text-gray-200 dark:hover:bg-gray-700 rounded">
                    @lang('livewire-powergrid::datatable.labels.all')
                </a>
                @if($checkbox)
                    <a x-on:click="$wire.call('exportToXLS', true); open = false"
                       href="#"
                       class="px-2 block text-gray-800 hover:bg-gray-50 hover:text-black-300 dark:text-gray-200 dark:hover:bg-gray-700 rounded">
                        @lang('livewire-powergrid::datatable.labels.selected')
                    </a>
                @endif
            </div>
        @endif
        @if(in_array('csv',$exportType))
            <div class="flex px-4 py-2 text-gray-400 dark:text-gray-300">
                <span class="w-12">Csv</span>
                <a x-on:click="$wire.call('exportToCsv'); open = false" href="#"
                   class="px-2 block text-gray-800 hover:bg-gray-50 hover:text-black-300 dark:text-gray-200 dark:hover:bg-gray-700 rounded">
                    @lang('livewire-powergrid::datatable.labels.all')
                </a>
                @if($checkbox)
                    <a x-on:click="$wire.call('exportToCsv', true); open = false" href="#"
                       class="px-2 block text-gray-800 hover:bg-gray-50 hover:text-black-300 dark:text-gray-200 dark:hover:bg-gray-700 rounded">
                        @lang('livewire-powergrid::datatable.labels.selected')
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
