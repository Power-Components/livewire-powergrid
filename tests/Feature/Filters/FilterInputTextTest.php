<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

it('properly filters by inputText', function (string $op, string $value, array $see, array $dontSee) {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-input-text';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata'],
                ['id' => 2, 'name' => 'Francesinha'],
                ['id' => 3, 'name' => 'Peixada'],
                ['id' => 4, 'name' => ''],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name', fn ($row) => (data_get($row, 'name') === '') ? 'EMPTY_ROW' : data_get($row, 'name'));
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    $lw = Livewire::test($component::class)
        ->set('filters', [
            'input_text' => ['name' => $value],
            'input_text_options' => ['name' => $op],
        ]);

    foreach ($see as $item) {
        $lw->assertSee($item);
    }
    foreach ($dontSee as $item) {
        $lw->assertDontSee($item);
    }
})->with([
    'contains' => ['contains', 'Pastel', ['Pastel de Nata'], ['Francesinha', 'Peixada']],
    'contains_not' => ['contains_not', 'Pastel', ['Francesinha', 'Peixada'], ['Pastel de Nata']],
    'is_empty' => ['is_empty', '', ['EMPTY_ROW'], ['Pastel de Nata', 'Francesinha', 'Peixada']],
    'is_not_empty' => ['is_not_empty', '', ['Pastel de Nata', 'Francesinha', 'Peixada'], ['EMPTY_ROW']],
    'starts_with' => ['starts_with', 'Pas', ['Pastel de Nata'], ['Francesinha', 'Peixada']],
    'ends_with' => ['ends_with', 'ada', ['Peixada'], ['Pastel de Nata', 'Francesinha']],
]);
