<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

it('properly filters by multi_select', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multi-select';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'category_id' => 2],
                ['id' => 3, 'name' => 'Dish 3', 'category_id' => 3],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::multiSelect('category_id')
                    ->dataSource(collect([
                        ['category_id' => 1, 'name' => 'Cat 1'],
                        ['category_id' => 2, 'name' => 'Cat 2'],
                        ['category_id' => 3, 'name' => 'Cat 3'],
                    ]))
                    ->optionValue('category_id')
                    ->optionLabel('name'),
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
        ->set('filters', [
            'multi_select' => ['category_id' => [1, 2]],
        ])
        ->assertSee('Dish 1')
        ->assertSee('Dish 2')
        ->assertDontSee('Dish 3')
        ->set('filters', [
            'multi_select' => ['category_id' => [3]],
        ])
        ->assertDontSee('Dish 1')
        ->assertDontSee('Dish 2')
        ->assertSee('Dish 3');
});

it('accepts a Closure dataSource and resolves it when filters render', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multi-select-lazy';

        public static int $loads = 0;

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::multiSelect('category_id')
                    ->dataSource(function () {
                        self::$loads++;

                        return collect([['category_id' => 1, 'name' => 'Cat 1']]);
                    })
                    ->optionValue('category_id')
                    ->optionLabel('name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Category', 'category_id'),
            ];
        }
    };

    $component::$loads = 0;

    Livewire::test($component::class);

    expect($component::$loads)->toBe(1);
});

it('resolves an inline Closure dataSource once across table interactions', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multi-select-inline-once';

        public static int $loads = 0;

        public function filterPosition(): string
        {
            return 'inline';
        }

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'category_id' => 2],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::multiSelect('category_id')
                    ->dataSource(function () {
                        self::$loads++;

                        return collect([['category_id' => 1, 'name' => 'Cat 1']]);
                    })
                    ->optionValue('category_id')
                    ->optionLabel('name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Category', 'category_id'),
            ];
        }
    };

    $component::$loads = 0;

    $test = Livewire::test($component::class);

    expect($component::$loads)->toBe(1);

    $test->call('sortBy', 'name')
        ->set('search', 'Dish')
        ->call('gotoPage', 2, 'page');

    expect($component::$loads)->toBe(1);
});

it('defers a Closure dataSource until the dropdown panel is loaded', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multi-select-defer';

        public static int $loads = 0;

        public function filterPosition(): string
        {
            return 'dropdown';
        }

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::multiSelect('category_id')
                    ->dataSource(function () {
                        self::$loads++;

                        return collect([['category_id' => 1, 'name' => 'Cat 1']]);
                    })
                    ->optionValue('category_id')
                    ->optionLabel('name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Category', 'category_id'),
            ];
        }
    };

    $component::$loads = 0;

    $test = Livewire::test($component::class);

    expect($component::$loads)->toBe(0);

    $test->call('loadFilterPanel');

    expect($component::$loads)->toBe(1);
});

it('ignores empty values instead of dropping the multi_select filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multi-select-empty';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'category_id' => 2],
            ]);
        }

        public function filters(): array
        {
            return [Filter::multiSelect('category_id')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('category_id');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name'), Column::make('Category', 'category_id')];
        }
    };

    Livewire::test($component::class)
        ->set('filters', [
            'multi_select' => ['category_id' => ['', 1]],
        ])
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2');
});
