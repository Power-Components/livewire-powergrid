<div class="flex mr-2 mt-2 sm:mt-0 gap-3">
    <button @click="$dispatch('toggle-filters-{{ $tableName }}')" type="button"
            class="pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-500 dark:hover:bg-pg-primary-700
    dark:ring-offset-pg-primary-800 dark:text-pg-primary-400 dark:bg-pg-primary-600">
        <x-livewire-powergrid::icons.filter class="h-4 w-4 text-pg-primary-500 dark:text-pg-primary-300" />
    </button>
</div>
