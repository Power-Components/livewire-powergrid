<div class="dt--top-section">
    <div class="row">

        <div class="col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center">

            @include($theme->base. ''.$theme->name.'.export')

            @includeIf($theme->base. ''.$theme->name.'.toggle-columns')

            @include('livewire-powergrid::components.frameworks.bootstrap5.loading')

        </div>
        <div class="col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3">

            @include($theme->base. ''.$theme->name.'.filter')

        </div>

    </div>
</div>

<div class="col-md-12 d-flex" style="margin-top: 5px">
@foreach($enabledFilters as $field => $filter)
    <div wire:click.prevent="clearFilter('{{ $field }}')" style="cursor: pointer; padding-right: 4px">
        <span class="badge rounded-pill bg-light text-dark">{{ $filter['label'] }}
         <svg width="10" stroke="currentColor" fill="none" viewBox="0 0 8 8">
            <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
        </svg>
        </span>
    </div>
@endforeach
</div>
