@inject('actionRulesClass', 'PowerComponents\LivewirePowerGrid\Components\Rules\RulesController')

<tbody>
    @foreach ($data as $row)
        @php
            $rowId = data_get($row, $primaryKey);

            $class = $theme->table->trBodyClass ?? null;

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
                @include('livewire-powergrid::components.row', [
                    'rowIndex' => $loop->index + 1,
                    'radio' => $radio,
                    'checkbox' => $checkbox,
                    'setUp' => $setUp,
                    'radioAttribute' => $radioAttribute,
                    'checkboxAttribute' => $checkboxAttribute,
                    'columns' => $columns,
                    'tableName' => $tableName,
                    'primaryKey' => $primaryKey,
                ])
                <tr
                    x-show="detailState"
                    style="{{ data_get($theme, 'table.trBodyStyle') }}"
                    {{ $trAttributesBag }}
                >
                    @include('livewire-powergrid::components.table.detail', [
                        'setUp' => $setUp,
                        'primaryKey' => $primaryKey,
                    ])
                </tr>
            </tbody>
        @else
            <tr
                wire:key="tbody-{{ $row->{$primaryKey} }}"
                style="{{ data_get($theme, 'table.trBodyStyle') }}"
                {{ $trAttributesBag }}
            >
                @include('livewire-powergrid::components.row', [
                    'rowIndex' => $loop->index + 1,
                    'radio' => $radio,
                    'checkbox' => $checkbox,
                    'setUp' => $setUp,
                    'radioAttribute' => $radioAttribute,
                    'checkboxAttribute' => $checkboxAttribute,
                    'columns' => $columns,
                    'tableName' => $tableName,
                    'primaryKey' => $primaryKey,
                ])
            </tr>
        @endif

        @includeWhen(isset($setUp['responsive']), 'livewire-powergrid::components.expand-container')
    @endforeach
</tbody>
