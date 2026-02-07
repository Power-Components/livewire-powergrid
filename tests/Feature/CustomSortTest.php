<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\{DishesCustomSortCollectionTable, DishesCustomSortTable};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

beforeEach(function () {
    TestDatabase::seed(customSortDishes());
});

it('uses custom sort callback for database queries', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'calories')
        ->assertSee('Dish A')
        ->assertSee('Dish B')
        ->call('sortBy', 'calories')
        ->assertSee('Dish A')
        ->assertSee('Dish B');
})->with('custom sort');

it('enables sort when using sortUsing callback', function () {
    $component = livewire(DishesCustomSortTable::class);

    // Get the columns and check that price column has sorting enabled
    $columns = $component->instance()->columns();

    $priceColumn = collect($columns)->firstWhere('field', 'price');

    expect($priceColumn->enableSort)->toBeTrue()
        ->and($priceColumn->sortable)->toBeTrue()
        ->and($priceColumn->sortCallback)->toBeInstanceOf(Closure::class);
});

it('uses custom sort callback for collection datasource', function () {
    livewire(DishesCustomSortCollectionTable::class)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'calories')
        ->set('sortDirection', 'asc')
        ->assertSeeInOrder(['Apple Pie', 'Cherry Tart', 'Zebra Dish', 'Banana Split', 'Donut'])
        ->call('sortBy', 'calories')
        ->set('sortDirection', 'desc')
        ->assertSeeInOrder(['Donut', 'Zebra Dish', 'Banana Split', 'Apple Pie', 'Cherry Tart']);
});

it('getSortCallback returns null for regular sortable columns', function () {
    $component = livewire(DishesCustomSortTable::class)->instance();

    // Regular sortable column should return null
    expect($component->getSortCallback('id'))->toBeNull()
        ->and($component->getSortCallback('name'))->toBeNull();
});

it('getSortCallback returns closure for columns with custom sort', function () {
    $component = livewire(DishesCustomSortTable::class)->instance();

    // Columns with custom sort callback should return the closure
    expect($component->getSortCallback('price'))->toBeInstanceOf(Closure::class)
        ->and($component->getSortCallback('calories'))->toBeInstanceOf(Closure::class);
});

it('sortCallback is excluded from toLivewire serialization', function () {
    $component = livewire(DishesCustomSortTable::class)->instance();
    $columns = $component->columns();

    $priceColumn = collect($columns)->firstWhere('field', 'price');

    // Ensure sortCallback exists on the column object
    expect($priceColumn->sortCallback)->toBeInstanceOf(Closure::class);

    // But it should be excluded from the serialized array
    $serialized = $priceColumn->toLivewire();
    expect($serialized)->not->toHaveKey('sortCallback');
});

dataset('custom sort', [
    'tailwind' => [DishesCustomSortTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
    'bootstrap' => [DishesCustomSortTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
]);

function customSortDishes(): array
{
    return [
        [
            'name' => 'Dish A',
            'category_id' => 7,
            'price' => 100.00,
            'stored_at' => '1',
            'calories' => 100,
            'serving_at' => 'pool bar',
            'diet' => 1,
            'in_stock' => true,
            'produced_at' => '2021-10-01',
        ],
        [
            'name' => 'Dish B',
            'category_id' => 7,
            'price' => 200.00,
            'stored_at' => '2',
            'calories' => 200,
            'serving_at' => 'pool bar',
            'diet' => 1,
            'in_stock' => true,
            'produced_at' => '2021-10-02',
        ],
        [
            'name' => 'Dish C',
            'category_id' => 7,
            'price' => 300.00,
            'stored_at' => '3',
            'calories' => 300,
            'serving_at' => 'pool bar',
            'diet' => 1,
            'in_stock' => true,
            'produced_at' => '2021-10-03',
        ],
        [
            'name' => 'Dish D',
            'category_id' => 7,
            'price' => 150.00,
            'stored_at' => '4',
            'calories' => 400,
            'serving_at' => 'pool bar',
            'diet' => 1,
            'in_stock' => true,
            'produced_at' => '2021-10-04',
        ],
        [
            'name' => 'Dish E',
            'category_id' => 7,
            'price' => 500.00,
            'stored_at' => '5',
            'calories' => 500,
            'serving_at' => 'pool bar',
            'diet' => 1,
            'in_stock' => true,
            'produced_at' => '2021-10-05',
        ],
    ];
}
