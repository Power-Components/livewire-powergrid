@inject('actionRulesClass', 'PowerComponents\LivewirePowerGrid\Components\Rules\RulesController')

<x-livewire-powergrid::table-base
    :theme="$theme->table"
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
            @foreach ($data as $row)
                @if (!isset($row->{$checkboxAttribute}) && $checkbox)
                    @php throw new Exception('To use checkboxes, you must define a unique key attribute in your data source.') @endphp
                @endif
                @php
                    $rowId = data_get($row, $primaryKey);

                    $class = $theme->table->trBodyClass;

                    $rulesValues = $actionRulesClass->recoverFromAction($row, 'pg:rows');

                    $applyRulesLoop = true;

                    $trAttributesBag = new \Illuminate\View\ComponentAttributeBag();
                    $trAttributesBag = $trAttributesBag->merge(['class' => $class]);

                    if (method_exists($this, 'actionRules')) {
                        $applyRulesLoop = $actionRulesClass->loop($this->actionRules($row), $loop);
                    }

                    if (filled($rulesValues['setAttributes']) && $applyRulesLoop) {
                        foreach ($rulesValues['setAttributes'] as $rulesAttributes) {
                            $trAttributesBag = $trAttributesBag->merge([
                                $rulesAttributes['attribute'] => $rulesAttributes['value'],
                            ]);
                        }
                    }
                @endphp

                @if (isset($setUp['detail']))
                    <tbody
                        wire:key="tbody-{{ $row->{$primaryKey} }}"
                        {{ $trAttributesBag }}
                        x-data="{ detailState: @entangle('setUp.detail.state.' . $row->{$primaryKey}) }"
                    >
                        @include('livewire-powergrid::components.row', ['rowIndex' => $loop->index + 1])
                        <tr
                            x-show="detailState"
                            style="{{ $theme->table->trBodyStyle }}"
                            {{ $trAttributesBag }}
                        >
                            @include('livewire-powergrid::components.table.detail')
                        </tr>
                    </tbody>
                @else
                    <tr
                        wire:key="tbody-{{ $row->{$primaryKey} }}"
                        style="{{ $theme->table->trBodyStyle }}"
                        {{ $trAttributesBag }}
                    >
                        @include('livewire-powergrid::components.row', ['rowIndex' => $loop->index + 1])
                    </tr>
                @endif

                @includeWhen(isset($setUp['responsive']), 'livewire-powergrid::components.expand-container')
            @endforeach

            @includeWhen($footerTotalColumn, 'livewire-powergrid::components.table-footer')
        @endif
    </x-slot:body>
</x-livewire-powergrid::table-base>
