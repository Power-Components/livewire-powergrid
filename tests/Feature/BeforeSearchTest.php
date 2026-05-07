<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('searches data using beforeSearch', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-before-search';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel'],
                ['id' => 2, 'name' => 'Francesinha'],
                ['id' => 3, 'name' => 'Peixada'],
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

        public function beforeSearchName(string $search): string
        {
            return 'Peixada';
        }
    };

    Livewire::test($component::class)
        ->set('search', 'Pastel')
        ->assertSee('Peixada')
        ->assertDontSee('Pastel')
        ->set('search', 'Francesinha')
        ->assertSee('Peixada')
        ->assertDontSee('Francesinha');
});

it('can use beforeSearch in boolean field', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-before-search-boolean';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata', 'in_stock' => '1'],
                ['id' => 2, 'name' => 'Carne Louca', 'in_stock' => '1'],
                ['id' => 3, 'name' => 'Barco-Sushi Simples', 'in_stock' => '0'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('in_stock');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchable(),
                Column::make('Stock', 'in_stock')->searchable(),
            ];
        }

        public function beforeSearch(string $field, string $search): string
        {
            if ($field === 'in_stock') {
                return $search === 'with_stock' ? '1' : '0';
            }

            return $search;
        }
    };

    Livewire::test($component::class)
        // without_stock => in_stock = 0
        ->set('search', 'without_stock')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Carne Louca')
        ->assertSee('Barco-Sushi Simples')
        // with_stock => in_stock = 1
        ->set('search', 'with_stock')
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Barco-Sushi Simples');
});
