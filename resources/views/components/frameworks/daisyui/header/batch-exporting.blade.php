<div>
    @php
        $queues = data_get($setUp, 'exportable.batchExport.queues', 0);
    @endphp
    @if ($queues > 0 && $showExporting)
        @if ($batchExporting && !$batchFinished)
            <div
                wire:poll="updateExportProgress"
                class="w-full my-3 px-4 rounded bg-base-200 py-3 text-center"
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
                        class="px-4 py-3 rounded-md cursor-pointer bg-base-200 shadow"
                        x-on:click="show =!show"
                    >
                        <div class="flex justify-between">
                            <span>
                                ⚡ {{ trans('livewire-powergrid::datatable.export.completed') }}
                            </span>
                            <x-livewire-powergrid::icons.chevron-double-down
                                class="w-5"
                            />
                        </div>
                    </div>
                    <div
                        x-show="show"
                        class="border-l border-b border-r border-base-200 px-2 py-4"
                    >
                        @foreach ($exportedFiles as $file)
                            <div class="flex w-full p-2">
                                <x-livewire-powergrid::icons.download
                                    class="w-5 mr-3"
                                />
                                <a
                                    class="cursor-pointer"
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
