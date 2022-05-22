<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8">

            @include($theme->layout->header, [
                'enabledFilters' => $enabledFilters
            ])

            @if(config('livewire-powergrid.filter') === 'outside')
                @if(count($makeFilters) > 0)
                    <div>
                        <x-livewire-powergrid::frameworks.tailwind.filter
                            :makeFilters="$makeFilters"
                            :inputTextOptions="$inputTextOptions"
                            :tableName="$tableName"
                            :filters="$filters"
                            :theme="$theme"
                        />
                    </div>
                @endif
            @endif

            <div class="{{ $theme->table->divClass }}" style="{{ $theme->table->divStyle }}">
                @include($table)
            </div>

            @include($theme->footer->view)
        </div>
    </div>
</div>
