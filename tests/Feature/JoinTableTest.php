<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('properly sorts and searches with simulated join in collection', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-join';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata', 'category' => ['name' => 'Sobremesas']],
                ['id' => 2, 'name' => 'Borsch', 'category' => ['name' => 'Sopas']],
                ['id' => 3, 'name' => 'Arroz', 'category' => ['name' => 'Acompanhamentos']],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_name', fn ($row) => data_get($row, 'category.name'));
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id')->sortable(),
                Column::make('Name', 'name')->searchable(),
                Column::make('Category', 'category_name', 'category.name')->sortable()->searchable(),
            ];
        }
    };

    Livewire::test($component::class)
        // Sort by simulated join field
        ->call('sortBy', 'category.name')
        ->set('sortDirection', 'asc')
        ->assertSeeInOrder(['Acompanhamentos', 'Sobremesas', 'Sopas'])
        ->set('sortDirection', 'desc')
        ->assertSeeInOrder(['Sopas', 'Sobremesas', 'Acompanhamentos'])

        // Search by simulated join field
        ->set('search', 'Sobremesas')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Sopas')
        ->assertDontSee('Acompanhamentos');
});
