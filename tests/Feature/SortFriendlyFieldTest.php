<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

uses()->group('sort');

/**
 * Column with a qualified dataField (dishes.name). The header must sort by the
 * friendly, URL-safe field ("name"), while the query still orders by the real
 * dataField ("dishes.name").
 */
function friendlySortComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'friendly-sort';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Banana'],
                ['id' => 2, 'name' => 'Apple'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name', 'dishes.name')->sortable(),
            ];
        }
    };
}

it('renders the header sort trigger with the friendly field, not the dotted dataField', function () {
    $html = Livewire::test(friendlySortComponent()::class)->html();

    expect($html)->toContain("sortBy('name')")
        ->and($html)->not->toContain("sortBy('dishes.name')");
});

it('maps the friendly sort field back to the dataField for the query', function () {
    $component = friendlySortComponent();

    expect($component->resolveSortField('name'))->toBe('dishes.name');
});

it('accepts both the friendly field and the dataField as valid sort fields', function () {
    $component = friendlySortComponent();

    expect($component->isValidSortField('name'))->toBeTrue()
        ->and($component->isValidSortField('dishes.name'))->toBeTrue();
});

it('sorts by the friendly field through the Livewire action', function () {
    Livewire::test(friendlySortComponent()::class)
        ->call('sortBy', 'name')
        ->assertSet('sortField', 'name')
        ->assertSeeInOrder(['Apple', 'Banana']);
});
