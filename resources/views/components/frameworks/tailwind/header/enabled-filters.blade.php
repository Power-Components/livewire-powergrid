@if(count($enabledFilters))
    <div class="w-full pt-3 mb-3">
        @if(count($enabledFilters) > 1)
            <span
                class="cursor-pointer inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 mr-1 text-sm font-medium bg-slate-500 text-white dark:bg-slate-600 dark:text-slate-200">
              {{ trans('livewire-powergrid::datatable.buttons.clear_all_filters') }}
              <button type="button"
                      wire:click.prevent="clearAllFilters"
                      class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-white dark:text-slate-200 hover:bg-slate-400 hover:text-slate-500 focus:outline-none focus:bg-slate-500 focus:text-slate-300 dark:focus:text-slate-500">
                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                  <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
                </svg>
              </button>
            </span>
        @endif
        @foreach($enabledFilters as $field => $filter)
            <span
                class="cursor-pointer border border-slate-200 inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 text-sm font-medium bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-300">
              {{ $filter['label'] }}
              <button type="button"
                      wire:click.prevent="clearFilter('{{ $field }}')"
                      class="flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-200 hover:text-slate-500 focus:outline-none focus:bg-slate-500 focus:text-slate-200 dark:hover:bg-slate-300 dark:hover:text-slate-500">
                <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                  <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
                </svg>
              </button>
            </span>
        @endforeach
    </div>
@endif
