@if($queues && $showExporting)

    @if($batchExporting && !$batchFinished)
        <div wire:poll="updateExportProgress"
             class="my-3 px-4 rounded-md py-3 shadow-sm text-center">
            <div>{{ trans('livewire-powergrid::datatable.export.exporting') }}</div>
            <div
                class="bg-emerald-500 rounded text-center"
                style="background-color: rgb(16 185 129); height: 0.25rem; width: {{ $batchProgress }}%; transition: width 300ms;">
            </div>
        </div>

        <div wire:poll="updateExportProgress"
             class="my-3 px-4 rounded-md py-3 shadow-sm text-center">
            <div>{{ $batchProgress }}%</div>
            <div>{{ trans('livewire-powergrid::datatable.export.exporting') }}</div>
        </div>
    @endif

    @if($batchFinished)
        <div class="my-3">
            <p>
                <button class="btn btn-primary"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseCompleted"
                        aria-expanded="false"
                        aria-controls="collapseCompleted">
                    ⚡ {{ trans('livewire-powergrid::datatable.export.completed') }}
                </button>
            </p>
            <div class="collapse" id="collapseCompleted">
                <div class="card card-body">
                    @foreach($exportedFiles as $file)
                        <div class="d-flex w-full p-2" style="cursor:pointer">
                            <x-livewire-powergrid::icons.download
                                style="width: 1.5rem;
                                           margin-right: 6px;
                                           color: #2d3034;"/>
                            <a
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
