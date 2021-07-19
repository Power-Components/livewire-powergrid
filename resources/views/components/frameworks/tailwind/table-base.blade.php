<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8">

            @include($theme->layout->header, [
                'enabledFilters' => $enabledFilters
            ])

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                @include($table) <!-- livewire-powergrid::components.table -->
            </div>

            @include($theme->footer->view)
        </div>
    </div>
</div>
