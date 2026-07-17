<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\NestedRelationSearchTable;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('search', 'relation');

beforeEach(function () {
    TestDatabase::up();
});

/**
 * The existing SearchHandlerTest only covers a string-shaped relationSearch
 * ('category' => 'name'), which hits the simple orWhereHas branch. These target
 * SearchHandler::filterNestedRelation (the array-shaped branch + the leftJoin
 * fallback when the relation name is not a real Eloquent relation), which had
 * no coverage.
 */
it('searches a database query using an array-shaped relationSearch', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'db-relation-search-array';

        public function datasource()
        {
            return Dish::query()->with('category');
        }

        public function relationSearch(): array
        {
            return [
                'category' => ['name'],
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_name', fn (Dish $dish) => $dish->category->name ?? '');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name')->searchable(),
                Column::make('Category', 'category_name'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'Sobremesas') // category of "Pastel de Nata"
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia') // Carnes
        ->assertDontSee('Francesinha vegana');   // Peixe
});

it('searches through a nested relation that falls back to a join', function () {
    // NestedRelationSearchTable declares `chef => ['name', 'restaurants' => ['name']]`.
    // `restaurants` is NOT a real relation on Chef (it is `restaurant`), so the
    // handler falls back to the join it declares in query().
    Livewire::test(NestedRelationSearchTable::class)
        ->set('search', 'Not McDonalds') // restaurant name
        ->assertSee('Pastel de Nata');
});

it('searches by the chef relation column and returns nothing for an unknown term', function () {
    Livewire::test(NestedRelationSearchTable::class)
        ->set('search', 'Luan')          // chef name (all dishes)
        ->assertSee('Pastel de Nata')
        ->set('search', 'no-such-chef-or-restaurant')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
});
