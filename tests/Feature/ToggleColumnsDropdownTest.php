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

it('seeds the draft from the current column visibility on mount', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-seed')::class);

    expect($test->get('draftColumns'))->toBe(['id' => true, 'name' => true, 'price' => true]);
});

it('does not change columns until applyColumns is called', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-defer')::class)
        ->set('draftColumns.price', false);

    expect(columnHidden($test->get('columns'), 'price'))->toBeFalse();
});

it('hides a column on applyColumns', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-apply')::class)
        ->set('draftColumns.price', false)
        ->call('applyColumns');

    expect(columnHidden($test->get('columns'), 'price'))->toBeTrue()
        ->and(columnHidden($test->get('columns'), 'name'))->toBeFalse();
});

it('counts hidden columns for the trigger badge', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-count')::class);

    expect($test->instance()->hiddenColumnsCount())->toBe(0);

    $test->set('draftColumns.price', false)
        ->set('draftColumns.name', false)
        ->call('applyColumns');

    expect($test->instance()->hiddenColumnsCount())->toBe(2);
});

it('resetColumns restores the declared default visibility and applies it', function () {
    $test = Livewire::test(toggleColumnsComponent('tc-reset')::class)
        ->set('draftColumns.price', false)
        ->call('applyColumns');

    expect(columnHidden($test->get('columns'), 'price'))->toBeTrue();

    $test->call('resetColumns');

    expect($test->get('draftColumns.price'))->toBeTrue()
        ->and(columnHidden($test->get('columns'), 'price'))->toBeFalse();
});

it('persists columns by default so a refresh keeps the applied visibility', function () {
    Config::set('livewire-powergrid.persist_driver', 'session');

    Livewire::test(toggleColumnsComponent('tc-persist')::class)
        ->set('draftColumns.price', false)
        ->call('applyColumns');

    expect(session('pg:toggle-columns'))->not->toBeNull()
        ->and(session('pg:toggle-columns'))->toContain('columns');
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
        ->set('draftColumns.price', false)
        ->call('applyColumns');

    expect(session('pg:tc-no-persist'))->toBeNull();
});
