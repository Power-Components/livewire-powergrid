@if ($multiSort && count($sortArray) > 0)
    <div class="w-full items-center flex pt-3 mb-3 gap-2">
        <span class="dark:text-pg-primary-300 text-sm">@lang('livewire-powergrid::datatable.multi_sort.message')</span>
        <span class="flex gap-2 select-none">
            @foreach ($sortArray as $field => $sort)
                @php
                    $label = $this->getLabelFromColumn($field);
                @endphp
                <span
                    wire:key="{{ $tableName }}-multi-sort-{{ $field }}"
                    wire:click.prevent="sortBy('{{ $field }}')"
                    title="{{ __(':label :sort', ['label' => $label, 'sort' => $sort]) }}"
                    class="group gap-2 cursor-pointer border border-pg-primary-200 inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 text-sm font-medium bg-pg-primary-100 text-pg-primary-700 dark:bg-pg-primary-700 dark:text-pg-primary-300"
                >
                    {{ $label }}
                    @if ($sort == 'desc')
                        <x-livewire-powergrid::icons.chevron-down class="w-4 h-4 group-hover:hidden" />
                        <x-livewire-powergrid::icons.x class="w-4 h-4 hidden group-hover:block transition-all" />
                    @else
                        <x-livewire-powergrid::icons.chevron-up class="w-4 h-4" />
                    @endif
                </span>
            @endforeach
        </span>
    </div>
@endif
