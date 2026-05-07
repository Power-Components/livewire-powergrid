<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('can render actionsFromView property', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-actions-view';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1'],
                ['id' => 2, 'name' => 'Dish 2'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::action('Action'),
            ];
        }

        public function actionsFromView($row)
        {
            return view('livewire-powergrid::tests.actions-view', compact('row'));
        }
    };

    Livewire::test($component::class)
        ->assertSeeInOrder([
            'Dish From Actions View: 1',
            'Dish From Actions View: 2',
        ]);
});
