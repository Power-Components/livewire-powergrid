<div x-data="{ open: false }" wire:key="toggle-columns-dropdown-{{ $tableName }}" class="relative">
    @if (data_get($setUp, 'header.toggleColumns'))
        <button
            type="button"
            @click="open = !open"
            @click.outside="open = false"
            class="{{ theme('header.layout.actions') }}"
        >
            <x-livewire-powergrid::icons.eye-off class="h-4 w-4" />
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div
            x-show="open"
            x-transition
            class="absolute left-0 z-50 mt-1 min-w-[10rem] rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800 shadow-lg py-1"
        >
            @foreach ($this->visibleColumns as $column)
                <button
                    type="button"
                    wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})"
                    class="w-full text-left px-3 py-2 text-sm transition-colors {{ data_get($column, 'hidden') ? 'text-zinc-400 dark:text-zinc-500 hover:bg-zinc-100 dark:hover:bg-white/8' : 'text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/8' }}"
                >
                    {!! data_get($column, 'title') !!}
                </button>
            @endforeach
        </div>
    @endif
</div>
