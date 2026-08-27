<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('properly sorts and searches in collection', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-column-core';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish A'], ['id' => 2, 'name' => 'Dish B']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Id', 'id')->sortable(), Column::make('Name', 'name')->searchable()->sortable()];
        }
    };

    Livewire::test($component::class)
        ->call('sortBy', 'name')
        ->set('sortDirection', 'desc')
        ->assertSeeInOrder(['Dish B', 'Dish A'])
        ->set('search', 'Dish A')
        ->assertSee('Dish A')
        ->assertDontSee('Dish B');
});

it('add contentClasses on column', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-column-classes';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1'], ['id' => 2, 'name' => 'Dish 2']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->contentClasses('bg-custom-500'),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('<span class=" bg-custom-500">');
});

it('add contentClasses on column using array', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-column-classes-array';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1'], ['id' => 2, 'name' => 'Dish 2']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->contentClasses(['Dish 2' => 'bg-custom-500']),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtmlInOrder([
            '<div>Dish 1</div>',
            '</span>',
            '<span class=" bg-custom-500">',
            '<div>Dish 2</div>',
        ]);
});

it('applies column align classes on header and body cells', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-column-align';

        public function datasource()
        {
            return collect([['id' => 1, 'in_stock' => 'Yes', 'price' => '10']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('in_stock')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('In stock', 'in_stock')->align('center'),
                Column::make('Price', 'price')->align('end'),
            ];
        }
    };

    $html = Livewire::test($component::class)->html();

    expect($html)
        ->toMatch('/<td class="[^"]*justify-center text-center[^"]*"[^>]*data-column="in_stock"/')
        ->toMatch('/<td class="[^"]*justify-end text-right[^"]*"[^>]*data-column="price"/')
        ->toMatch('/<div class="[^"]*justify-center text-center[^"]*"[^>]*>\s*<span data-value>In stock<\/span>/');
});
