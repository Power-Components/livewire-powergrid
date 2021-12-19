@if(count($enabledFilters))
    <div class="w-full pt-3 mb-3">
        @foreach($enabledFilters as $field => $filter)
            <span
                class="cursor-pointer inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 text-sm font-medium bg-teal-100 text-teal-700 dark:bg-teal-400 dark:text-teal-900">
              {{ $filter['label'] }}
              <button type="button"
                      wire:click.prevent="clearFilter('{{ $field }}')"
                      class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-teal-600 dark:text-teal-900 hover:bg-teal-200 hover:text-teal-500 focus:outline-none focus:bg-teal-500 focus:text-white">
                <span class="sr-only">{{ $filter['label'] }}></span>
                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                  <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
                </svg>
              </button>
        </span>
        @endforeach
    </div>
@endif
