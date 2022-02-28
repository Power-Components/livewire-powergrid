@if($queues && $showExporting)

    @if($batchExporting && !$batchFinished)
        <div wire:poll="updateExportProgress"
             class="w-full my-3 px-4 rounded py-3 bg-gray-50 shadow-sm dark:bg-gray-500 text-center">
            <div class="dark:text-gray-300">{{ $batchProgress }}%</div>
            <div class="dark:text-gray-300">{{ trans('livewire-powergrid::datatable.export.exporting') }}</div>
        </div>
    @endif

    @if($batchFinished)
        <div class="w-full my-3 dark:bg-gray-800">
            <div x-data={show:true} class="rounded-top">
                <div class="px-4 py-3 rounded-md cursor-pointer bg-gray-50 shadow dark:bg-gray-500"
                     @click="show=!show">
                    <div class="flex justify-between">
                        <button
                            class="appearance-none text-left text-base font-medium text-gray-500 focus:outline-none dark:text-gray-300"
                            type="button">
                            ⚡ {{ trans('livewire-powergrid::datatable.export.completed') }}
                        </button>
                        <x-livewire-powergrid::icons.chevron-double-down class="w-5 dark:text-gray-200"/>
                    </div>
                </div>
                <div x-show="show"
                     class="border-l border-b border-r border-gray-200 dark:border-gray-600 px-2 py-4 dark:border-0 dark:bg-gray-700">
                    @foreach($exportedFiles as $file)
                        <div class="flex w-full p-2">
                            <x-livewire-powergrid::icons.download
                                class="w-5 text-gray-700 dark:text-gray-300 mr-3"/>
                            <a class="cursor-pointer text-gray-600 dark:text-gray-300"
                               wire:click="downloadExport('{{ $file }}')">
                                {{ $file }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endif
