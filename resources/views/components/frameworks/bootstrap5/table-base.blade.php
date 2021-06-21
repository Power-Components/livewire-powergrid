<div>
    <div class="col-md-12">
        @include($theme->layout->header, [
                'filtersEnabled' => $filters_enabled
        ])
    </div>
    <div class="table-responsive col-md-12">
        @include($table)
    </div>
    <div class="col-md-12">
        <x-livewire-powergrid::footer>
            <x-slot name="perPage">
                @include($theme->perPage->view)
            </x-slot>
        </x-livewire-powergrid::footer>
    </div>
</div>


