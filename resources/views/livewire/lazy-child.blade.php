@use('PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager')
@inject('actionRulesClass', 'PowerComponents\LivewirePowerGrid\Components\Rules\RulesController')

<tbody>
    @foreach ($data as $row)
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
            <tr {{ $trAttributesBag }}>
                @include('livewire-powergrid::components.row', [
                    'rowIndex'   => $loop->index + 1,
                    'childIndex' => $childIndex
                ])
            </tr>
            @if (data_get($setUp, 'detail.state.' . $rowId))
                <tr
                    style="{{ data_get($theme, 'table.trBodyStyle') }}"
                    {{ $trAttributesBag }}
                >
                    @include('livewire-powergrid::components.table.detail')
                </tr>
            @endif
        @else
            <tr
                style="{{ data_get($theme, 'table.trBodyStyle') }}"
                {{ $trAttributesBag }}
            >
                @include('livewire-powergrid::components.row', [
                    'rowIndex' => $loop->index + 1,
                ])
            </tr>
        @endif

        @includeWhen(isset($setUp['responsive']), 'livewire-powergrid::components.expand-container')
    @endforeach
</tbody>
