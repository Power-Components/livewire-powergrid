@if ($multiSort && count($sortArray) > 0)
    <div class="w-full items-center flex pt-3 mb-3 gap-2">
        <span class="dark:text-zinc-300 text-sm">@lang('livewire-powergrid::datatable.multi_sort.message')</span>
        <span class="flex gap-2 select-none">
            @foreach ($sortArray as $field => $sort)
                @php
                    $label = $this->getLabelFromColumn($field);
                @endphp
                <span
                    wire:key="{{ $tableName }}-multi-sort-{{ $field }}"
                    wire:click.prevent="sortBy('{{ $field }}')"
                    title="{{ __(':label :sort', ['label' => $label, 'sort' => $sort]) }}"
                    class="group cursor-pointer select-none rounded-md outline-none inline-flex items-center border px-2 py-0.5 font-bold text-xs border-zinc-300 bg-white dark:border-2 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:text-zinc-400 text-zinc-600 hover:text-zinc-500"
                >
                    {{ $label }}
                    @if ($sort == 'desc')
                        <x-livewire-powergrid::icons.chevron-down class="w-4 h-4 ml-1 group-hover:hidden" />
                        <x-livewire-powergrid::icons.x class="w-4 h-4 ml-1 hidden group-hover:block transition-all" />
                    @else
                        <x-livewire-powergrid::icons.chevron-up class="w-4 h-4 ml-1" />
                    @endif
                </span>
            @endforeach
        </span>
    </div>
@endif
