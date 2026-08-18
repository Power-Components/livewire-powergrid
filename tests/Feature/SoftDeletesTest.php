<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

it('properly handles soft deletes', function () {
    // Setup data
    Dish::query()->forceDelete();
    $d1 = Dish::create(['name' => 'Dish 1', 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'price' => 10, 'calories' => 100, 'stored_at' => '1', 'produced_at' => now()]);
    $d2 = Dish::create(['name' => 'Dish 2', 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'price' => 20, 'calories' => 200, 'stored_at' => '1', 'produced_at' => now()]);
    $d2->delete();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-soft-deletes';

        public function datasource()
        {
            return Dish::query();
        }

        public function setUp(): array
        {
            return [
                PowerGrid::header()->showSoftDeletes(),
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
        // Default: only undeleted
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2')

        // withTrashed
        ->dispatch('pg:softDeletes-test-soft-deletes', softDeletes: 'withTrashed')
        ->assertSee('Dish 1')
        ->assertSee('Dish 2')

        // onlyTrashed
        ->dispatch('pg:softDeletes-test-soft-deletes', softDeletes: 'onlyTrashed')
        ->assertDontSee('Dish 1')
        ->assertSee('Dish 2')

        // back to default
        ->dispatch('pg:softDeletes-test-soft-deletes', softDeletes: '')
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2');
})->requiresSQLite();

it('shows warning messages when enabled', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-soft-deletes-msg';

        public function datasource()
        {
            return Dish::query();
        }

        public function setUp(): array
        {
            return [
                PowerGrid::header()->showSoftDeletes(showMessage: true),
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
        ->dispatch('pg:softDeletes-test-soft-deletes-msg', softDeletes: 'withTrashed')
        ->assertSee(trans('livewire-powergrid::datatable.soft_deletes.message_with_trashed'))
        ->dispatch('pg:softDeletes-test-soft-deletes-msg', softDeletes: 'onlyTrashed')
        ->assertSee(trans('livewire-powergrid::datatable.soft_deletes.message_only_trashed'));
})->requiresSQLite();
