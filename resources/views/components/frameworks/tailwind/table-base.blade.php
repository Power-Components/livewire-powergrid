<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8">

            @include($theme->layout->header, [
                'filtersEnabled' => $filters_enabled
            ])

            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                @include($table)
            </div>

            <x-livewire-powergrid::footer>
                <x-slot name="perPage">
                    @include($theme->perPage->view)
                </x-slot>
            </x-livewire-powergrid::footer>

        </div>
    </div>
</div>
