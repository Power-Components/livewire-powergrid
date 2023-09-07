<div>
    @php
        $queues = data_get($setUp, 'exportable.batchExport.queues', 0);
    @endphp
    @if ($queues > 0 && $showExporting)
        @if ($batchExporting && !$batchFinished)
            <div
                wire:poll="updateExportProgress"
                class="w-full my-3 px-4 rounded dark:text-pg-primary-300 bg-pg-primary-100 dark:bg-pg-primary-800 py-3 text-center"
            >
                <div>{{ trans('livewire-powergrid::datatable.export.exporting') }}</div>
                <span class="font-normal text-xs">{{ $batchProgress }}%</span>
                <div
                    class="bg-emerald-500 rounded h-1 text-center"
                    style="width: {{ $batchProgress }}%; transition: width 300ms;"
                >
                </div>
            </div>
        @endif

        @if ($batchFinished)
            <div class="w-full my-3 dark:bg-pg-primary-800">
                <div
                    x-data={show:true}
                    class="rounded-top"
                >
                    <div
                        class="px-4 py-3 rounded-md cursor-pointer bg-pg-primary-100 shadow dark:bg-pg-primary-800"
                        x-on:click="show =!show"
                    >
                        <div class="flex justify-between">
                            <button
                                class="appearance-none text-left text-base font-medium text-pg-primary-500 focus:outline-none dark:text-pg-primary-300"
                                type="button"
                            >
                                ⚡ {{ trans('livewire-powergrid::datatable.export.completed') }}
                            </button>
                            <x-livewire-powergrid::icons.chevron-double-down
                                class="w-5 text-pg-primary-400 dark:text-pg-primary-200"
                            />
                        </div>
                    </div>
                    <div
                        x-show="show"
                        class="border-l border-b border-r border-pg-primary-200 dark:border-pg-primary-600 px-2 py-4 dark:border-0 dark:bg-pg-primary-700"
                    >
                        @foreach ($exportedFiles as $file)
                            <div class="flex w-full p-2">
                                <x-livewire-powergrid::icons.download
                                    class="w-5 text-pg-primary-700 dark:text-pg-primary-300 mr-3"
                                />
                                <a
                                    class="cursor-pointer text-pg-primary-600 dark:text-pg-primary-300"
                                    wire:click="downloadExport('{{ $file }}')"
                                >
                                    {{ $file }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
