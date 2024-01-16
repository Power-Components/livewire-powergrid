<?php

use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesSoftDeletesTable;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

beforeEach(
    function () {
        TestDatabase::seed(dishesUndeleted());
        DB::table('dishes')->insert(dishesDeleted());
    }
);

it('should display softDeletes button', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('setUp.footer.perPage', '10')
        ->set('softDeletes', '')
        ->assertSeeHtml('M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16')
        ->assertSeeHtml("dispatch('pg:softDeletes-default', {softDeletes: 'withTrashed'})")
        ->assertSeeHtml("dispatch('pg:softDeletes-default', {softDeletes: 'onlyTrashed'})")
        ->assertSeeHtml("dispatch('pg:softDeletes-default', {softDeletes: ''})");
})->with('soft_deletes');

it('should list only undeleted records', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('setUp.footer.perPage', '10')
        ->set('softDeletes', '')
        ->assertSeeHtml('Dish C')
        ->assertSeeHtml('Dish D')
        ->assertSeeHtml('Dish E')
        ->assertSeeHtml('Dish F')
        ->assertSeeHtml('Dish G')
        ->assertSeeHtml('Dish H')
        ->assertSeeHtml('Dish I')
        ->assertDontSeeHtml('Dish A')
        ->assertDontSeeHtml('Dish B');
})->with('soft_deletes');

it('should list all records including excluded', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('setUp.footer.perPage', '20')
        ->set('softDeletes', 'withTrashed')
        ->assertSeeHtml('Dish A')
        ->assertSeeHtml('Dish B')
        ->assertSeeHtml('Dish E')
        ->assertSeeHtml('Dish F')
        ->assertSeeHtml('Dish G')
        ->assertSeeHtml('Dish H')
        ->assertSeeHtml('Dish I');
})->with('soft_deletes')->skip();

it('should list only deleted records', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('setUp.footer.perPage', '10')
        ->set('softDeletes', 'onlyTrashed')
        ->assertDontSeeHtml('Dish C')
        ->assertDontSeeHtml('Dish D')
        ->assertDontSeeHtml('Dish E')
        ->assertDontSeeHtml('Dish F')
        ->assertDontSeeHtml('Dish G')
        ->assertDontSeeHtml('Dish H')
        ->assertDontSeeHtml('Dish I')
        ->assertSeeHtml('Dish A')
        ->assertSeeHtml('Dish B');
})->with('soft_deletes')->skip();

it('should be able to see a warning message when showMessageSoftDeletes is true and softDeletes === withTrashed or onlyTrashed', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('setUp.footer.perPage', '10')
        ->set('setUp.header.showMessageSoftDeletes', true)
        ->assertDontSee(trans('livewire-powergrid::datatable.soft_deletes.message_with_trashed'))
        ->assertDontSeeHtml(trans('livewire-powergrid::datatable.soft_deletes.message_only_trashed'))
        ->set('softDeletes', 'withTrashed')
        ->assertDontSee(trans('livewire-powergrid::datatable.soft_deletes.message_only_trashed'))
        ->assertSee(trans('livewire-powergrid::datatable.soft_deletes.message_with_trashed'))
        ->set('softDeletes', 'onlyTrashed')
        ->assertSee(trans('livewire-powergrid::datatable.soft_deletes.message_only_trashed'))
        ->assertDontSee(trans('livewire-powergrid::datatable.soft_deletes.message_with_trashed'))
        ->set('setUp.header.showMessageSoftDeletes', false)
        ->assertDontSee(trans('livewire-powergrid::datatable.soft_deletes.message_with_trashed'))
        ->assertDontSee(trans('livewire-powergrid::datatable.soft_deletes.message_only_trashed'))
        ->set('softDeletes', 'withTrashed')
        ->assertDontSee(trans('livewire-powergrid::datatable.soft_deletes.message_with_trashed'))
        ->assertDontSee(trans('livewire-powergrid::datatable.soft_deletes.message_only_trashed'));
})->with('soft_deletes');

dataset('soft_deletes', [
    [DishesSoftDeletesTable::class, 'tailwind'],
    [DishesSoftDeletesTable::class, 'bootstrap'],
]);

/**
 * Small Dish dataset for sorting test
 *
 * @return array
 */
function dishesDeleted(): array
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
            'produced_at' => '2021-10-03',
            'deleted_at'  => '2021-10-03',
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
            'produced_at' => '2021-10-03',
            'deleted_at'  => '2021-10-03',
        ],
    ];
}

function dishesUndeleted(): array
{
    return [
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
