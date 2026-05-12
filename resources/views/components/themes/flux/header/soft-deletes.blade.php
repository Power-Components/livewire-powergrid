<div x-data="{ open: false }" wire:key="soft-deletes-dropdown-{{ $tableName }}" class="relative">
    @if (data_get($setUp, 'header.softDeletes'))
        <button
            type="button"
            @click="open = !open"
            @click.outside="open = false"
            class="{{ theme('header.layout.actions') }}"
        >
            <x-livewire-powergrid::icons.trash class="h-4 w-4" />
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div
            x-show="open"
            x-transition
            class="absolute left-0 z-50 mt-1 min-w-[12rem] rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800 shadow-lg py-1"
        >
            <button
                type="button"
                wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: ''})"
                @click="open = false"
                class="w-full text-left px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/8 transition-colors"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
            </button>
            <button
                type="button"
                wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'withTrashed'})"
                @click="open = false"
                class="w-full text-left px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/8 transition-colors"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
            </button>
            <button
                type="button"
                wire:click="$dispatch('pg:softDeletes-{{ $tableName }}', { softDeletes: 'onlyTrashed'})"
                @click="open = false"
                class="w-full text-left px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/8 transition-colors"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
            </button>
        </div>
    @endif
</div>
