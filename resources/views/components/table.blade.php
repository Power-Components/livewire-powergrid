@inject('actionRulesClass', 'PowerComponents\LivewirePowerGrid\Components\Rules\RulesController')

<x-livewire-powergrid::table-base
    :theme="data_get($theme, 'table')"
    :items="$items"
    :ready-to-load="$readyToLoad"
>
    <x-slot:header>
        @include('livewire-powergrid::components.table.tr')
    </x-slot:header>

    <x-slot:loading>
        @include('livewire-powergrid::components.table.tr', ['loading' => true])
    </x-slot:loading>

    <x-slot:body>
        @if ($this->hasColumnFilters)
            @include('livewire-powergrid::components.inline-filters')
        @endif

        @if (is_null($data) || count($data) === 0)
            @include('livewire-powergrid::components.table.th-empty')
        @else
            @includeWhen($headerTotalColumn, 'livewire-powergrid::components.table-header')

            @foreach (range(0, $items) as $item)
                <livewire:load-more-children
                    wire:key="{{ $item }}"
                    :$item
                    :primary-key="$primaryKey"
                    :radio="$radio"
                    :radio-attribute="$radioAttribute"
                    :checkbox="$checkbox"
                    :checkbox-attribute="$checkboxAttribute"
                    :columns="$this->visibleColumns"
                    :theme="$theme"
                    :data="$this->getCachedData
                        ->skip($item * $this->rowsPerChildComponent)
                        ->take($this->rowsPerChildComponent)"
                />
            @endforeach

        @endif
    </x-slot:body>
</x-livewire-powergrid::table-base>
