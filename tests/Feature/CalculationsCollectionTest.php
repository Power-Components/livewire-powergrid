<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('calculates properly from collection', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-calculations-collection';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Luan', 'balance' => 100],
                ['id' => 2, 'name' => 'Daniel', 'balance' => 200],
                ['id' => 3, 'name' => 'Claudio', 'balance' => 300],
                ['id' => 4, 'name' => 'Vitor', 'balance' => 400],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('balance');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id')->withCount('Count Id', true, true),
                Column::make('Name', 'name')->searchable(),
                Column::make('Balance', 'balance')
                    ->withSum('Sum Balance', true, true)
                    ->withAvg('Avg Balance', true, true)
                    ->withMin('Min Balance', true, true)
                    ->withMax('Max Balance', true, true),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('Count Id: 4')
        ->assertSee('Sum Balance: 1000')
        ->assertSee('Avg Balance: 250')
        ->assertSee('Min Balance: 100')
        ->assertSee('Max Balance: 400')

        ->set('search', 'Luan')
        ->assertSee('Count Id: 1')
        ->assertSee('Sum Balance: 100')
        ->assertSee('Avg Balance: 100')
        ->assertSee('Min Balance: 100')
        ->assertSee('Max Balance: 100');
});
