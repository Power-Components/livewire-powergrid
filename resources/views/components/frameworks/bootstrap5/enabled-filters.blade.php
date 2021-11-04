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
