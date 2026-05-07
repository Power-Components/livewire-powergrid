<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

it('properly filters by multiple filters and clear all', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multiple-filters';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel', 'price' => 10, 'in_stock' => true],
                ['id' => 2, 'name' => 'Francesinha', 'price' => 20, 'in_stock' => true],
                ['id' => 3, 'name' => 'Peixada', 'price' => 30, 'in_stock' => false],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
                Filter::boolean('in_stock'),
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
        // Filter by price and in_stock
        ->set('filters', [
            'number' => ['price' => ['start' => 15]],
            'boolean' => ['in_stock' => 'true'],
        ])
        ->assertDontSee('Pastel')
        ->assertSee('Francesinha')
        ->assertDontSee('Peixada')

        // Add text filter
        ->set('filters.input_text.name', 'Peixada')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Peixada') // Because in_stock is true and Peixada is false

        // Clear all
        ->call('clearAllFilters')
        ->assertSee('Pastel')
        ->assertSee('Francesinha')
        ->assertSee('Peixada');
});
