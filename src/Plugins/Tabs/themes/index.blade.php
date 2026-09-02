@props([
    'tableName' => null,
    'tabs' => [],
    'activeTab' => null,
])

<div
    wire:partial="pg-tabs-{{ $tableName }}"
    wire:key="pg-tabs-{{ $tableName }}"
    class="pg-tabs flex justify-center mb-3"
>
    @if (filled($tabs))
        <div class="inline-flex flex-wrap items-center gap-1 rounded-xl border border-gray-200 bg-white p-1 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    wire:click="selectTab('{{ $tab['key'] }}')"
                    wire:key="pg-tab-{{ $tableName }}-{{ $tab['key'] }}"
                    @class([
                        'inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium transition',
                        'bg-gray-100 text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' => $tab['active'],
                        'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100' => ! $tab['active'],
                    ])
                    @if ($tab['active']) aria-current="page" @endif
                >
                    <span>{{ $tab['label'] }}</span>

                    @if (! is_null($tab['badge']))
                        <span @class([
                            'inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold',
                            'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' => $tab['active'],
                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => ! $tab['active'],
                        ])>{{ $tab['badge'] }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    @endif
</div>
