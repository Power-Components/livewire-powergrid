<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('searches data using relation search in collection', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-relation-search';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata', 'category' => ['name' => 'Sobremesas']],
                ['id' => 2, 'name' => 'Borsch', 'category' => ['name' => 'Sopas']],
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
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchable(),
                Column::make('Category', 'category_name', 'category.name')->searchable(),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'Sobremesas')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Borsch')
        ->set('search', 'Sopas')
        ->assertSee('Borsch')
        ->assertDontSee('Pastel de Nata')
        ->set('search', 'Borsch')
        ->assertSee('Borsch')
        ->assertDontSee('Pastel de Nata');
});
