<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('deferLoading work properly', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-defer-loading';

        public bool $deferLoading = true;

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
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
        ->assertDontSee('Dish 1')
        ->call('fetchDatasource')
        ->assertSee('Dish 1');
});
