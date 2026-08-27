<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('collection detail', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-detail';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Name 1'],
                ['id' => 2, 'name' => 'Name 2'],
            ]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::detail()->view('livewire-powergrid::tests.detail'),
            ];
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
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('Name 1')
        ->assertDontSee('Id 2')
        ->call('toggleDetail', '2')
        ->assertSee('Id 2');
});
