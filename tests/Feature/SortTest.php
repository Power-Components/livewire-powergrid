<?php

use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

beforeEach(
    function () {
        DB::table('dishes')->truncate();
        $this->seeders(dishesForSorting());
    }
);

it('properly sorts ASC/DESC with: date')
    ->livewire(DishesTable::class)
    ->set('perPage', '10')
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

it('properly sorts ASC/DESC with: int')
    ->livewire(DishesTable::class)
    ->set('perPage', '10')
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

it('properly sorts ASC/DESC with: string')
    ->livewire(DishesTable::class)
    ->set('perPage', '10')
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

it('properly sorts ASC/DESC with: boolean')
    ->livewire(DishesTable::class)
    ->set('perPage', '10')
    ->call('sortBy', 'in_stock')
    ->set('sortDirection', 'asc')
    ->assertSeeHtml('Dish L')
    ->assertSeeHtml('Dish K')
    ->set('sortDirection', 'desc')
    ->assertDontSeeHtml('Dish L')
    ->assertDontSeeHtml('Dish K');

it('properly sorts ASC/DESC with: string-number')
    ->livewire(DishesTable::class)
    ->set('perPage', '10')
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

/**
 * Small Dish dataset for sorting test
 *
 * @return array
 */
function dishesForSorting(): array
{
    return  [
        [
            "name" => "Dish A",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "1",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-01"
        ],
        [
            "name" => "Dish B",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "2",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-02"
        ],
        [
            "name" => "Dish C",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "3",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-03"
        ],
        [
            "name" => "Dish D",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "4",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-04"
        ],
        [
            "name" => "Dish E",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "5",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-05"
        ],
        [
            "name" => "Dish F",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "6",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-06"
        ],
        [
            "name" => "Dish G",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "7",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-07"
        ],
        [
            "name" => "Zebra Dish H",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "8",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-08"
        ],
        [
            "name" => "Dish I",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "9",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-09"
        ],
        [
            "name" => "Dish J",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "10",
            "calories" => 224,
            "in_stock" => true,
            "produced_at" => "2021-10-10"
        ],
        [
            "name" => "Dish K",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "1b",
            "calories" => 224,
            "in_stock" => false,
            "produced_at" => "2021-02-01"
        ],
        [
            "name" => "Dish L",
            "category_id" => 7,
            "price" => 174.22,
            "stored_at" => "1a",
            "calories" => 224,
            "in_stock" => false,
            "produced_at" => "2021-01-01"
        ],
    ];
}
