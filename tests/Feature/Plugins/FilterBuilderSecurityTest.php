<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\Filter, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder\{FilterBuilderHandler, FilterBuilderValidator};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('filters', 'filter-builder', 'security');

function fbSecurityComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-security';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()->maxConditions(1)];
        }

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel', 'price' => 10.00],
                ['id' => 2, 'name' => 'Carne', 'price' => 30.00],
                ['id' => 3, 'name' => 'Sushi', 'price' => 60.00],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
            ];
        }
    };
}

function fbSecurityMeta(): array
{
    $component = fbSecurityComponent();
    $component->columns = $component->columns();

    return FilterBuilderValidator::columnsMeta($component);
}

it('drops a condition whose column is not in the filters() allowlist', function () {
    Livewire::test(fbSecurityComponent()::class)
        ->set('filterBuilder', ['match' => 'and', 'rows' => [
            ['column' => 'secret_admin_flag', 'operator' => 'is', 'value' => '1', 'value2' => null],
        ]])
        // forged column is ignored → nothing is filtered
        ->assertSee('Pastel')
        ->assertSee('Carne')
        ->assertSee('Sushi');
});

it('drops a condition whose operator is not whitelisted for the type', function () {
    $meta = fbSecurityMeta();

    $result = FilterBuilderValidator::validate([
        'match' => 'and',
        'rows' => [
            ['column' => 'name', 'operator' => '= 1 OR 1=1', 'value' => 'x', 'value2' => null],
            ['column' => 'price', 'operator' => 'between', 'value' => '1', 'value2' => '2'],
        ],
    ], $meta, 30);

    expect($result['rows'])->toHaveCount(1)
        ->and($result['rows'][0]['column'])->toBe('price');
});

it('rejects column identifiers with unexpected characters', function () {
    $meta = fbSecurityMeta();

    $result = FilterBuilderValidator::validate([
        'match' => 'and',
        'rows' => [
            ['column' => 'name); drop table dishes;--', 'operator' => 'contains', 'value' => 'x'],
        ],
    ], $meta, 30);

    expect($result['rows'])->toBeEmpty();
});

it('sanitizes an invalid match mode to AND', function () {
    $meta = fbSecurityMeta();

    $result = FilterBuilderValidator::validate([
        'match' => 'union select password from users',
        'rows' => [['column' => 'name', 'operator' => 'contains', 'value' => 'x']],
    ], $meta, 30);

    expect($result['match'])->toBe('and');
});

it('enforces the maxConditions cap', function () {
    $meta = fbSecurityMeta();

    $result = FilterBuilderValidator::validate([
        'match' => 'and',
        'rows' => [
            ['column' => 'name', 'operator' => 'contains', 'value' => 'a'],
            ['column' => 'name', 'operator' => 'contains', 'value' => 'b'],
            ['column' => 'name', 'operator' => 'contains', 'value' => 'c'],
        ],
    ], $meta, 1);

    expect($result['rows'])->toHaveCount(1);
});

it('drops value-required conditions with a blank value', function () {
    $meta = fbSecurityMeta();

    $result = FilterBuilderValidator::validate([
        'match' => 'and',
        'rows' => [
            ['column' => 'name', 'operator' => 'contains', 'value' => ''],       // dropped
            ['column' => 'price', 'operator' => 'between', 'value' => '1'],       // dropped: missing value2
            ['column' => 'name', 'operator' => 'is_empty'],                        // kept: valueless
        ],
    ], $meta, 30);

    expect($result['rows'])->toHaveCount(1)
        ->and($result['rows'][0]['operator'])->toBe('is_empty');
});

it('binds values as query parameters and never inlines raw SQL', function () {
    TestDatabase::up();

    $component = fbSecurityComponent();
    $component->columns = $component->columns();
    $component->setUp = ['filterBuilder' => PowerGrid::filterBuilder()];
    $component->filterBuilder = ['match' => 'and', 'rows' => [
        ['column' => 'name', 'operator' => 'contains', 'value' => "x'; DROP TABLE dishes; --", 'value2' => null],
    ]];

    $query = Dish::query();
    (new FilterBuilderHandler($component))->apply($query);

    $sql = $query->toSql();

    expect($sql)->toContain('?')
        ->and($sql)->not->toContain('DROP TABLE')
        ->and($query->getBindings())->toContain("%x'; DROP TABLE dishes; --%");

    // The table is still intact after building/executing the query.
    $query->get();

    expect(Dish::query()->count())->toBeGreaterThan(0);
})->group('database');

it('drops conditions on unsupported filter types', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-unsupported';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()];
        }

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Pastel']]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::multiSelect('category'), // unsupported by the builder in v1
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name'), Column::make('Category', 'category')];
        }
    };

    $component->columns = $component->columns();

    $meta = collect(FilterBuilderValidator::columnsMeta($component));

    expect($meta->keys()->all())->toBe(['name'])
        ->and($meta->has('category'))->toBeFalse();
});
