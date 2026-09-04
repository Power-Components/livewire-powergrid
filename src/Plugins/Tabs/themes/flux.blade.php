@props([
    'tableName' => null,
    'tabs' => [],
    'activeTab' => null,
    'align' => 'left',
])

<div
    wire:partial="pg-tabs-{{ $tableName }}"
    wire:key="pg-tabs-{{ $tableName }}"
    @class([
        'pg-tabs flex w-full',
        'justify-start' => $align === 'left',
        'justify-center' => $align === 'center',
        'justify-end' => $align === 'right',
    ])
>
    @if (filled($tabs))
        <div class="inline-flex flex-wrap items-center gap-1 rounded-xl border border-zinc-200 p-1 dark:border-white/10">
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    wire:click="selectTab('{{ $tab['key'] }}')"
                    wire:key="pg-tab-{{ $tableName }}-{{ $tab['key'] }}"
                    @class([
                        'inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                        'bg-zinc-100 text-zinc-900 shadow-sm dark:bg-white/10 dark:text-white' => $tab['active'],
                        'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100' => ! $tab['active'],
                    ])
                    @if ($tab['active']) aria-current="page" @endif
                >
                    @if (filled($tab['icon']))
                        <flux:icon :name="$tab['icon']" variant="micro" />
                    @endif

                    <span>{{ $tab['label'] }}</span>

                    @if (! is_null($tab['badge']))
                        <span @class([
                            'inline-flex items-center justify-center rounded-md px-1.5 py-0.5 text-xs font-semibold',
                            'bg-accent text-accent-foreground' => $tab['active'],
                            'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300' => ! $tab['active'],
                        ])>{{ $tab['badge'] }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    @endif
</div>
