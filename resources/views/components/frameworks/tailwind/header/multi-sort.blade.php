@if($multiSort && count($sortArray) > 0)
    <div class="w-full flex pt-3 mb-3 gap-2">
        <span class="dark:text-slate-300">@lang('livewire-powergrid::datatable.multi_sort.message')</span>
        <span class="flex gap-2">
            @foreach($sortArray as $field => $sort)
                @php
                    $label = $this->getLabelFromColumn($field);
                @endphp
                <span
                    wire:key="{{ $tableName }}-multi-sort-{{ $field }}"
                    wire:click.prevent="sortBy('{{ $field }}')"
                    title="{{ __(':label :sort', ['label' => $label, 'sort' => $sort]) }}"
                    class="group gap-2 cursor-pointer border border-slate-200 inline-flex rounded-full items-center py-0.5 pl-2.5 pr-1 text-sm font-medium bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-300">
                    {{ $label }}
                    @if($sort == 'desc')
                        <x-livewire-powergrid::icons.chevron-down class="w-4 h-4 group-hover:hidden"/>
                        <x-livewire-powergrid::icons.x class="w-4 h-4 hidden group-hover:block transition-all"/>
                    @else
                        <x-livewire-powergrid::icons.chevron-up class="w-4 h-4"/>
                    @endif
                </span>
            @endforeach
        </span>
    </div>
@endif
