<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('dispatches event to toggle detail row', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-detail-row';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1'],
            ]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::detail()
                    ->view('livewire-powergrid::tests.detail'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->call('toggleDetail', 1)
        ->assertDispatched('pg-toggle-detail-test-detail-row-1');
});
