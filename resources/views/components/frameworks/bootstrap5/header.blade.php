<div class="dt--top-section">
    <div class="row">

        <div class="col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center">

            @if($exportOption)
                @include($theme->base. ''.$theme->name.'.export')
            @endif

            @includeIf($theme->base. ''.$theme->name.'.toggle-columns')

            @include('livewire-powergrid::components.frameworks.bootstrap5.loading')

        </div>
        <div class="col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3">

            @include($theme->base. ''.$theme->name.'.filter')

        </div>

    </div>
</div>


@if($queues && $showExporting)

    @if($batchExporting && !$batchFinished)
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

@if(count($enabledFilters))
    <div class="col-md-12 d-flex" style="margin-top: 5px">
        @foreach($enabledFilters as $field => $filter)
            <div wire:click.prevent="clearFilter('{{ $field }}')"
                 style="cursor: pointer; padding-right: 4px">
            <span class="badge rounded-pill bg-light text-dark">{{ $filter['label'] }}
                 <svg width="10" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
                </svg>
            </span>
            </div>
        @endforeach
    </div>
@endif
