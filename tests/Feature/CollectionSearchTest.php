<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('searches from collection', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-collection-search';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Name 1'],
                ['id' => 2, 'name' => 'Name 2'],
                ['id' => 3, 'name' => 'Name 3'],
                ['id' => 4, 'name' => 'Name 4'],
                ['id' => 5, 'name' => 'Name 5'],
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
                Column::make('Name', 'name')->searchable(),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'Name 1')
        ->assertSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 4')
        ->assertDontSee('Name 5')
        ->set('search', 'Name 3')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertSee('Name 3')
        ->assertDontSee('Name 4')
        ->assertDontSee('Name 5')
        ->set('search', 'Name 5')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 4')
        ->assertSee('Name 5');
});
