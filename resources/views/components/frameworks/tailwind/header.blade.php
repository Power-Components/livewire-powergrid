<div class="md:flex md:flex-row w-full justify-between">

    <div class="md:flex md:flex-row w-full">

        <div class="">
            <x-livewire-powergrid::actions
                :theme="$theme"
                row=""
                :actions="$this->headers"/>
        </div>

        <div class="flex flex-row">

            @if($exportOption)
                <div class="mr-2 mt-2 sm:mt-0">
                    @includeIf($theme->base. ''.$theme->name.'.export')
                </div>
            @endif

            @includeIf($theme->base. ''.$theme->name.'.toggle-columns')

        </div>

        @include($theme->base. ''.$theme->name.'.loading')

    </div>

    @include($theme->base. ''.$theme->name.'.search')

</div>

@if($queues && $showExporting)

    @if($exporting && !$exportFinished)
        <div wire:poll="updateExportProgress"
             class="w-full my-3 px-4 rounded-md py-3 bg-gray-200 shadow-sm dark:bg-gray-500 text-center">
            <div class="dark:text-gray-300">{{ $progress }}%</div>
            <div class="dark:text-gray-300">{{ trans('livewire-powergrid::datatable.export.exporting') }}</div>
        </div>
    @endif

    @if($exportFinished)
        <div class="w-full my-3 dark:bg-gray-800">
            <div x-data={show:true} class="rounded-top">
                <div class="px-4 py-3 rounded-md cursor-pointer bg-gray-200 shadow dark:bg-gray-500"
                     @click="show=!show">
                    <div class="flex justify-between">
                        <button
                            class="appearance-none text-left text-base font-medium text-gray-500 focus:outline-none dark:text-gray-300"
                            type="button">
                            ⚡ {{ trans('livewire-powergrid::datatable.export.completed') }}
                        </button>
                        <x-livewire-powergrid::icons.chevron-double-down class="w-5 dark:text-gray-200"/>
                    </div>
                </div>
                <div x-show="show"
                     class="border-l border-b border-r border-gray-200 dark:border-gray-600 px-2 py-4 dark:border-0 dark:bg-gray-700">
                    @foreach($exportedFiles as $file)
                        <div class="flex w-full p-2">
                            <x-livewire-powergrid::icons.download
                                class="w-5 text-gray-700 dark:text-gray-300 mr-3"/>
                            <a class="cursor-pointer text-gray-600 dark:text-gray-300"
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
    <div class="w-full pt-3 mb-3">
        @foreach($enabledFilters as $field => $filter)
            <span
                class="cursor-pointer inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 text-sm font-medium bg-indigo-100 text-indigo-700">
              {{ $filter['label'] }}
              <button type="button"
                      wire:click.prevent="clearFilter('{{ $field }}')"
                      class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white">
                <span class="sr-only"{{ $filter['label'] }}></span>
                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                  <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
                </svg>
              </button>
        </span>

        @endforeach
    </div>
@endif
