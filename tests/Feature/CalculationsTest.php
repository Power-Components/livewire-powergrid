<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\Actions\TestDatabase;

beforeEach(
    function () {
        TestDatabase::seed(dishesForWithSum());
    }
);

it('calculate "count" on id field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSee('Count ID: 12')
        ->set('search', 'Dish C')
        ->assertSee('Count ID: 1');
})->with('calculations');

it('calculate "sum" on price field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('<span>Sum Price: 15000.6</span>')
        ->set('search', 'Dish C')
        ->assertSeeHtml('<span>Sum Price: 300.5</span>')
        ->set('search', 'Dish F')
        ->assertSeeHtml('<span>Sum Price: 600</span>');
})->with('calculations');

it('calculate "count" on price field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Count Price: 12')
        ->set('search', 'Dish C')
        ->assertSeeHtml('Count Price: 1')
        ->set('search', 'Dish F')
        ->assertSeeHtml('Count Price: 1');
})->with('calculations');

it('calculate "avg" on price field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('<span>Avg Price: 1250.05</span>');
})->with('calculations');

it('calculate "min" on price field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('<span>Min Price: 100</span>');
})->with('calculations');

it('calculate "max" on price field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('<span>Max Price: 7500</span>');
})->with('calculations');

/**
 * Small Dish dataset for sorting test
 *
 * @return array
 */
function dishesForWithSum(): array
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
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => false,
            'produced_at' => '2021-02-01',
        ],
        [
            'name'        => 'Dish L',
            'category_id' => 7,
            'price'       => 2000.00,
            'stored_at'   => '1a',
            'calories'    => 224,
            'serving_at'  => 'pool bar',
            'diet'        => 1,
            'in_stock'    => false,
            'produced_at' => '2021-01-01',
        ],
    ];
}
