<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('filters', 'inputtext', 'relation');

beforeEach(function () {
    TestDatabase::up();
});

/**
 * Exercises InputText::builderRelation() — filtering a model by a column that
 * lives on a related table (whereRelation). This whole code path had no tests.
 */
function relationInputTextComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'relation-input-text') {}

        public function datasource()
        {
            return Dish::query()->with('category');
        }

        public function filters(): array
        {
            return [
                Filter::inputText('category_name')
                    ->filterRelation('category', 'name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('category_name', fn (Dish $dish) => $dish->category->name);
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Category', 'category_name', 'category.name'),
            ];
        }
    };
}

it('filters by relation column using contains operator (default)', function () {
    Livewire::test(relationInputTextComponent('relation-contains')::class)
        ->set('filters.input_text.category_name', 'obrem') // Sobremesas
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia')  // Carnes
        ->assertDontSee('Francesinha vegana');    // Peixe
});

it('filters by relation column using is operator', function () {
    Livewire::test(relationInputTextComponent('relation-is')::class)
        ->set('filters.input_text_options.category_name.0', 'is')
        ->set('filters.input_text.category_name', 'Sobremesas')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana');
});

it('filters by relation column using is_not operator', function () {
    Livewire::test(relationInputTextComponent('relation-is-not')::class)
        ->set('filters.input_text_options.category_name.0', 'is_not')
        ->set('filters.input_text.category_name', 'Sobremesas')
        ->assertDontSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia');
});

it('filters by relation column using starts_with operator', function () {
    Livewire::test(relationInputTextComponent('relation-starts')::class)
        ->set('filters.input_text_options.category_name.0', 'starts_with')
        ->set('filters.input_text.category_name', 'Sopas')
        ->assertSee('борщ')            // Sopas
        ->assertDontSee('Pastel de Nata');
});

it('filters by relation column using ends_with operator', function () {
    Livewire::test(relationInputTextComponent('relation-ends')::class)
        ->set('filters.input_text_options.category_name.0', 'ends_with')
        ->set('filters.input_text.category_name', 'remesas') // Sobremesas
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana');
});

// Every category in the seed has a non-empty name, so the "presence" operators
// return rows and the "absence" operators return none — the point is to execute
// the whereRelation branches for each operator (builderRelation).
it('filters by relation column using presence operators', function (string $operator) {
    Livewire::test(relationInputTextComponent('relation-presence-'.$operator)::class)
        ->set('filters.input_text_options.category_name.0', $operator)
        ->set('filters.input_text.category_name', '')
        ->assertOk()
        ->assertSee('Pastel de Nata');
})->with(['is_not_empty', 'is_not_null', 'is_not_blank']);

it('filters by relation column using absence operators', function (string $operator) {
    Livewire::test(relationInputTextComponent('relation-absence-'.$operator)::class)
        ->set('filters.input_text_options.category_name.0', $operator)
        ->set('filters.input_text.category_name', '')
        ->assertOk()
        ->assertDontSee('Pastel de Nata');
})->with(['is_empty', 'is_null', 'is_blank']);
