<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishesTable, Concerns\Components\DishesTableWithJoin};

beforeEach(
    function () {
        TestDatabase::seed(dishesForSorting());
    }
);

it('properly sorts ASC/DESC with: date', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'produced_at')
        ->set('sortDirection', 'desc')
        ->assertSeeHtml('Dish J')
        ->assertSeeHtml('Dish I')
        ->assertSeeHtml('Dish H')
        ->assertSeeHtml('Dish G')
        ->assertSeeHtml('Dish F')
        ->assertSeeHtml('Dish E')
        ->assertSeeHtml('Dish D')
        ->assertDontSeeHtml('Dish K')
        ->assertDontSeeHtml('Dish L')
        ->call('sortBy', 'produced_at')
        ->set('sortDirection', 'asc')
        ->assertSeeHtml('Dish K')
        ->assertSeeHtml('Dish L')
        ->assertSeeHtml('Dish A')
        ->assertSeeHtml('Dish B')
        ->assertSeeHtml('Dish C')
        ->assertSeeHtml('Dish D')
        ->assertSeeHtml('Dish E')
        ->assertSeeHtml('Dish F')
        ->assertSeeHtml('Dish G')
        ->assertSeeHtml('Dish H')
        ->assertDontSeeHtml('Dish I')
        ->assertDontSeeHtml('Dish J');
})->with('sort_join');

it('properly sorts ASC/DESC with: int', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'id')
        ->set('sortDirection', 'desc')
        ->assertSeeHtml('Dish L')
        ->assertSeeHtml('Dish K')
        ->assertDontSeeHtml('Dish A')
        ->assertDontSeeHtml('Dish B')
        ->call('sortBy', 'id')
        ->set('sortDirection', 'asc')
        ->assertSeeHtml('Dish A')
        ->assertSeeHtml('Dish B')
        ->assertSeeHtml('Dish C');
})->with('sort_join');

it('properly sorts ASC/DESC with: string', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'name')
        ->set('sortDirection', 'desc')
        ->assertSeeHtml('Zebra Dish H')
        ->assertSeeHtml('Dish K')
        ->assertDontSeeHtml('Dish A')
        ->assertDontSeeHtml('Dish B')
        ->call('sortBy', 'name')
        ->set('sortDirection', 'asc')
        ->assertSeeHtml('Dish A')
        ->assertSeeHtml('Dish B')
        ->assertDontSeeHtml('Zebra Dish H');
})->with('sort_join');

it('properly sorts ASC/DESC with: float', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'price')
        ->set('sortDirection', 'desc')
        ->assertSeeHtml('Zebra Dish H')
        ->assertSeeHtml('Dish K')
        ->assertDontSeeHtml('Dish A')
        ->assertDontSeeHtml('Dish B')
        ->call('sortBy', 'price')
        ->set('sortDirection', 'asc')
        ->assertSeeHtml('Dish A')
        ->assertSeeHtml('Dish B')
        ->assertDontSeeHtml('Zebra Dish H');
})->with('sort_join');

it('properly sorts ASC/DESC with: boolean', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', '10')
        ->call('sortBy', 'in_stock')
        ->set('sortDirection', 'asc')
        ->assertSeeHtml('Dish L')
        ->assertSeeHtml('Dish K')
        ->set('sortDirection', 'desc')
        ->assertDontSeeHtml('Dish L')
        ->assertDontSeeHtml('Dish K');
})->with('sort_join');

it('properly sorts ASC/DESC with: string-number', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', '10')
        ->set('withSortStringNumber', true)
        ->set('ignoreTablePrefix', false)
        ->call('sortBy', 'stored_at')
        ->set('sortDirection', 'asc')
        ->assertSeeHtml('Dish K')
        ->assertSeeHtml('Dish L')
        ->assertSeeHtml('Dish A')
        ->assertSeeHtml('Dish B')
        ->assertSeeHtml('Dish C')
        ->assertSeeHtml('Dish D')
        ->assertSeeHtml('Dish E')
        ->assertSeeHtml('Dish F')
        ->assertSeeHtml('Dish G')
        ->assertSeeHtml('Dish H')
        ->assertDontSeeHtml('Dish I')
        ->set('sortDirection', 'desc')
        ->assertSeeHtml('Dish J')
        ->assertSeeHtml('Dish I')
        ->assertSeeHtml('Dish H')
        ->assertSeeHtml('Dish G')
        ->assertSeeHtml('Dish F')
        ->assertSeeHtml('Dish E')
        ->assertSeeHtml('Dish D')
        ->assertSeeHtml('Dish C')
        ->assertSeeHtml('Dish B')
        ->assertSeeHtml('Dish K')
        ->assertDontSeeHtml('Dish A');
})->with('sort_join');

dataset('sort_join', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);

/**
 * Small Dish dataset for sorting test
 *
 * @return array
 */
function dishesForSorting(): array
{
    return [
        [
            'name'        => 'Dish A',
            'category_id' => 7,
            'price'       => 100.00,
            'stored_at'   => '1',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-01',
        ],
        [
            'name'        => 'Dish B',
            'category_id' => 7,
            'price'       => 200.10,
            'stored_at'   => '2',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-02',
        ],
        [
            'name'        => 'Dish C',
            'category_id' => 7,
            'price'       => 300.50,
            'stored_at'   => '3',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-03',
        ],
        [
            'name'        => 'Dish D',
            'category_id' => 7,
            'price'       => 400.00,
            'stored_at'   => '4',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-04',
        ],
        [
            'name'        => 'Dish E',
            'category_id' => 7,
            'price'       => 500.00,
            'stored_at'   => '5',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-05',
        ],
        [
            'name'        => 'Dish F',
            'category_id' => 7,
            'price'       => 600.00,
            'stored_at'   => '6',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-06',
        ],
        [
            'name'        => 'Dish G',
            'category_id' => 7,
            'price'       => 700.00,
            'stored_at'   => '7',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-07',
        ],
        [
            'name'        => 'Zebra Dish H',
            'category_id' => 7,
            'price'       => 7500.00,
            'stored_at'   => '8',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-08',
        ],
        [
            'name'        => 'Dish I',
            'category_id' => 7,
            'price'       => 800.00,
            'stored_at'   => '9',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-09',
        ],
        [
            'name'        => 'Dish J',
            'category_id' => 7,
            'price'       => 900.00,
            'stored_at'   => '10',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => true,
            'produced_at' => '2021-10-10',
        ],
        [
            'name'        => 'Dish K',
            'category_id' => 7,
            'price'       => 1000.00,
            'stored_at'   => '1b',
            'calories'    => 224,
            'in_stock'    => false,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'produced_at' => '2021-02-01',
        ],
        [
            'name'        => 'Dish L',
            'category_id' => 7,
            'price'       => 2000.00,
            'stored_at'   => '1a',
            'calories'    => 224,
            'in_stock'    => false,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'produced_at' => '2021-01-01',
        ],
    ];
}
