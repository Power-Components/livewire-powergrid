@if(count($enabledFilters))
    <div class="w-full pt-3 mb-3">
        @if(count($enabledFilters) > 1)
            <span
                class="cursor-pointer inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 mr-1 text-sm font-medium bg-pg-primary-500 text-white dark:bg-pg-primary-600 dark:text-pg-primary-200">
              {{ trans('livewire-powergrid::datatable.buttons.clear_all_filters') }}
              <button type="button"
                      wire:click.prevent="clearAllFilters"
                      class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-white dark:text-pg-primary-200 hover:bg-pg-primary-400 hover:text-pg-primary-500 focus:outline-none focus:bg-pg-primary-500 focus:text-pg-primary-300 dark:focus:text-pg-primary-500">
                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                  <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
                </svg>
              </button>
            </span>
        @endif
        @foreach($enabledFilters as $field => $filter)
            <span
                class="cursor-pointer border border-pg-primary-200 inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 text-sm font-medium bg-pg-primary-100 text-pg-primary-700 dark:bg-pg-primary-600 dark:text-pg-primary-300">
              {{ $filter['label'] }}
              <button type="button"
                      wire:click.prevent="clearFilter('{{ $field }}')"
                      class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-pg-primary-600 dark:text-pg-primary-300 hover:bg-pg-primary-200 hover:text-pg-primary-500 focus:outline-none focus:bg-pg-primary-500 focus:text-pg-primary-200 dark:hover:bg-pg-primary-300 dark:hover:text-pg-primary-500">
                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                  <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
                </svg>
              </button>
            </span>
        @endforeach
    </div>
@endif
