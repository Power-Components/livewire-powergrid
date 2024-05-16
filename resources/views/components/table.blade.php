@inject('actionRulesClass', 'PowerComponents\LivewirePowerGrid\Components\Rules\RulesController')
@use('PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager')

<x-livewire-powergrid::table-base
    :ready-to-load="$readyToLoad"
    :theme="$theme"
    :table-name="$tableName"
    :lazy="!is_null(data_get($setUp, 'lazy'))"
>
    <x-slot:header>
        @include('livewire-powergrid::components.table.tr')
    </x-slot:header>

    <x-slot:loading>
        @include('livewire-powergrid::components.table.tr', ['loading' => true])
    </x-slot:loading>

    <x-slot:body>
        @includeWhen($this->hasColumnFilters, 'livewire-powergrid::components.inline-filters')

        @if (is_null($data) || count($data) === 0)
            @include('livewire-powergrid::components.table.th-empty')
        @else
            @includeWhen($headerTotalColumn, 'livewire-powergrid::components.table-header')

            @if (empty(data_get($setUp, 'lazy')))
                @foreach ($data as $row)
                    @if (!isset($row->{$checkboxAttribute}) && $checkbox)
                        @php throw new Exception('To use checkboxes, you must define a unique key attribute in your data source.') @endphp
                    @endif
                    @php
                        $rowId = data_get($row, $primaryKey);

                        $class = data_get($theme, 'table.trBodyClass');

                        $rulesValues = $actionRulesClass->recoverFromAction($row, RuleManager::TYPE_ROWS);

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
                            wire:key="tbody-{{ $rowId }}"
                            {{ $trAttributesBag }}
                            x-data="{ detailState: @entangle('setUp.detail.state.' . $rowId) }"
                        >
                            @include('livewire-powergrid::components.row', [
                                'rowIndex' => $loop->index + 1,
                            ])
                            <tr
                                x-show="detailState"
                                style="{{ data_get($theme, 'table.trBodyStyle') }}"
                                {{ $trAttributesBag }}
                            >
                                @include('livewire-powergrid::components.table.detail')
                            </tr>
                        </tbody>
                    @else
                        <tr
                            wire:key="tbody-{{ $rowId }}"
                            style="{{ data_get($theme, 'table.trBodyStyle') }}"
                            {{ $trAttributesBag }}
                        >
                            @include('livewire-powergrid::components.row', [
                                'rowIndex' => $loop->index + 1,
                            ])
                        </tr>
                    @endif

                    @includeWhen(isset($setUp['responsive']),
                        'livewire-powergrid::components.expand-container')
                @endforeach
            @else
                <div>
                    @foreach (range(0, data_get($setUp, 'lazy.items')) as $item)
                        @php
                            $skip = $item * data_get($setUp, 'lazy.rowsPerChildren');
                            $take = data_get($setUp, 'lazy.rowsPerChildren');
                        @endphp

                        <livewire:lazy-child
                            key="{{ $this->getLazyKeys }}"
                            :child-index="$item"
                            :$primaryKey
                            :$radio
                            :$radioAttribute
                            :$checkbox
                            :$checkboxAttribute
                            :$theme
                            :$setUp
                            :$tableName
                            :parentName="$this->getName()"
                            :columns="$this->visibleColumns"
                            :data="\PowerComponents\LivewirePowerGrid\ProcessDataSource::transform($data->skip($skip)->take($take), $this)"
                        />
                    @endforeach
                </div>
            @endif

            @includeWhen($footerTotalColumn, 'livewire-powergrid::components.table-footer')
        @endif
    </x-slot:body>
</x-livewire-powergrid::table-base>
