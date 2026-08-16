<?php

use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

uses()->group('sort');

function sortingComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'sorting-helpers';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'A']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')->sortable()];
        }
    };
}

it('toggles a field through asc, desc and removal in multi-sort', function () {
    $component = sortingComponent();
    $component->multiSort = true;

    $component->sortByArray('name');
    expect($component->sortArray)->toBe(['name' => 'asc']);

    $component->sortByArray('name');
    expect($component->sortArray)->toBe(['name' => 'desc']);

    $component->sortByArray('name');
    expect($component->sortArray)->toBe([]);
});

it('keeps independent directions for multiple fields', function () {
    $component = sortingComponent();
    $component->multiSort = true;

    $component->sortByArray('name');
    $component->sortByArray('price');

    expect($component->sortArray)->toBe(['name' => 'asc', 'price' => 'asc']);
});

it('renders the sort label arrows for single-sort state', function () {
    $component = sortingComponent();
    $component->multiSort = false;
    $component->sortField = 'name';

    $component->sortDirection = 'asc';
    ob_start();
    $component->sortLabel('name');
    expect(ob_get_clean())->toBe('&#8595;'); // down arrow for asc

    $component->sortDirection = 'desc';
    ob_start();
    $component->sortLabel('name');
    expect(ob_get_clean())->toBe('&#8593;'); // up arrow for desc

    // a field that is not the active sort field shows the neutral arrow
    ob_start();
    $component->sortLabel('other');
    expect(ob_get_clean())->toBe('&#8597;');
});

it('renders the sort label arrows for multi-sort state', function () {
    $component = sortingComponent();
    $component->multiSort = true;

    $component->sortArray = ['name' => 'desc'];
    ob_start();
    $component->sortLabel('name');
    expect(ob_get_clean())->toBe('&#8595;');

    $component->sortArray = ['name' => 'asc'];
    ob_start();
    $component->sortLabel('name');
    expect(ob_get_clean())->toBe('&#8593;');

    // field not in the sort array → neutral arrow
    ob_start();
    $component->sortLabel('price');
    expect(ob_get_clean())->toBe('&#8597;');
});

it('resolves the sort field with and without the table prefix', function () {
    $component = sortingComponent();
    $component->currentTable = 'dishes';
    $component->ignoreTablePrefix = false;

    expect($component->resolveSortField('name'))->toBe('dishes.name');

    // already-qualified fields are left untouched
    expect($component->resolveSortField('category.name'))->toBe('category.name');

    // when the prefix is ignored, the field is returned as-is
    $component->ignoreTablePrefix = true;
    expect($component->resolveSortField('price'))->toBe('price');
});
