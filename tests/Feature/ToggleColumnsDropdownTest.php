<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

uses()->group('toggle-columns');

function toggleColumnsComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'toggle-columns') {}

        public function setUp(): array
        {
            return [PowerGrid::header()->showToggleColumns()];
        }

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel', 'price' => 10],
                ['id' => 2, 'name' => 'Sushi', 'price' => 60],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
            ];
        }
    };
}

function columnHidden(array $columns, string $field): ?bool
{
    $column = collect($columns)->first(fn ($c) => data_get($c, 'field') === $field);

    return $column ? (bool) data_get($column, 'hidden') : null;
}

function columnFields(array $columns): array
{
    return collect($columns)->map(fn ($c) => data_get($c, 'field'))->all();
}

it('hides a column on applyColumns', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-apply')::class)
        ->call('applyColumns', [['field' => 'price', 'hidden' => true]]);

    expect(columnHidden($test->get('columns'), 'price'))->toBeTrue()
        ->and(columnHidden($test->get('columns'), 'name'))->toBeFalse()
        ->and($test->instance()->hiddenColumnsCount())->toBe(1);
});

it('reorders columns on applyColumns and keeps the order on later requests', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-order')::class)
        ->call('applyColumns', [
            ['field' => 'price', 'hidden' => false],
            ['field' => 'id', 'hidden' => true],
            ['field' => 'name', 'hidden' => false],
        ]);

    expect(columnFields($test->get('columns')))->toBe(['price', 'id', 'name']);

    $test->call('$refresh');

    expect(columnFields($test->get('columns')))->toBe(['price', 'id', 'name'])
        ->and(columnHidden($test->get('columns'), 'id'))->toBeTrue();

    $html = $test->html();

    expect(strpos($html, 'data-column="price"'))->toBeLessThan(strpos($html, 'data-column="id"'));
});

it('ignores unknown fields on applyColumns', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-unknown')::class)
        ->call('applyColumns', [
            ['field' => 'nope', 'hidden' => true],
            ['field' => 'name', 'hidden' => false],
            'garbage',
        ]);

    expect(columnFields($test->get('columns')))->toBe(['id', 'name', 'price']);
});

it('applies columns without rendering or querying the datasource', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'tc-no-query';

        public static int $queries = 0;

        public function setUp(): array
        {
            return [PowerGrid::header()->showToggleColumns()];
        }

        public function datasource()
        {
            self::$queries++;

            return collect([['id' => 1, 'name' => 'Pastel']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Id', 'id'), Column::make('Name', 'name')];
        }
    };

    $test = Livewire::test($component::class);
    $before = $component::$queries;

    $test->call('applyColumns', [['field' => 'name', 'hidden' => true], ['field' => 'id', 'hidden' => false]]);

    expect($component::$queries)->toBe($before);
});

it('resetColumns restores the declared order and visibility', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-reset')::class)
        ->call('applyColumns', [['field' => 'price', 'hidden' => true], ['field' => 'id', 'hidden' => false]]);

    expect(columnFields($test->get('columns')))->toBe(['price', 'name', 'id']);

    $test->call('resetColumns');

    expect(columnFields($test->get('columns')))->toBe(['id', 'name', 'price'])
        ->and(columnHidden($test->get('columns'), 'price'))->toBeFalse();
});

it('restores the persisted order and visibility on a fresh mount', function () {
    Config::set('livewire-powergrid.persist_driver', 'session');

    Livewire::test(toggleColumnsComponent('tc-restore')::class)
        ->call('applyColumns', [
            ['field' => 'price', 'hidden' => false],
            ['field' => 'name', 'hidden' => true],
            ['field' => 'id', 'hidden' => false],
        ]);

    $fresh = Livewire::test(toggleColumnsComponent('tc-restore')::class);

    expect(columnFields($fresh->get('columns')))->toBe(['price', 'name', 'id'])
        ->and(columnHidden($fresh->get('columns'), 'name'))->toBeTrue();
});

it('persists columns by default so a refresh keeps the applied visibility', function () {
    Config::set('livewire-powergrid.persist_driver', 'session');

    Livewire::test(toggleColumnsComponent('tc-persist')::class)
        ->call('applyColumns', [['field' => 'price', 'hidden' => true]]);

    expect(session('pg7:toggle-columns'))->not->toBeNull()
        ->and(session('pg7:toggle-columns'))->toContain('columns');
});

it('withoutPersist disables column persistence', function () {
    Config::set('livewire-powergrid.persist_driver', 'session');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'tc-no-persist';

        public function setUp(): array
        {
            return [PowerGrid::header()->showToggleColumns()];
        }

        public function boot(): void
        {
            $this->withoutPersist();
        }

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Pastel', 'price' => 10]]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name'), Column::make('Price', 'price')];
        }
    };

    Livewire::test($component::class)
        ->call('applyColumns', [['field' => 'price', 'hidden' => true]]);

    expect(session('pg7:tc-no-persist'))->toBeNull();
});
