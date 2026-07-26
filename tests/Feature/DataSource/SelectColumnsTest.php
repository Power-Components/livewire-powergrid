<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\DataSource\ProcessDataSource;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

/**
 * Capture the paginated data SELECT issued against the `dishes` table
 * (ignoring the pagination count(*) query).
 */
function captureDishesSelect(Closure $callback): string
{
    $queries = [];

    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });

    $callback();

    foreach (array_reverse($queries) as $sql) {
        if (str_contains($sql, 'from "dishes"') && ! str_contains($sql, 'count(*)')) {
            return $sql;
        }
    }

    return '';
}

it('prunes hidden + searchable columns from the display SELECT', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-prune-hidden-searchable';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('additional');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchable(),
                Column::make('Additional', 'additional')->searchable()->hidden(),
            ];
        }
    };

    $sql = captureDishesSelect(fn () => Livewire::test($component::class)->assertSee('Pastel de Nata'));

    expect($sql)
        ->not->toContain('select *')
        ->not->toContain('"additional"')
        ->toContain('"name"')
        ->toContain('"id"');
});

it('still searches on a pruned hidden + searchable column', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-search-pruned-column';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('additional');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Additional', 'additional')->searchable()->hidden(),
            ];
        }
    };

    // "Hot-roll" only exists inside the JSON `additional` column of the sushi dishes.
    Livewire::test($component::class)
        ->set('search', 'Hot-roll')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertDontSee('Pastel de Nata');
});

it('does not prune when the dataField is also used by a visible column', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-prune-shared-datafield';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            // Visible column shares the same dataField as the hidden+searchable one.
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Name Search', 'name_search', 'name')->searchable()->hidden(),
            ];
        }
    };

    $sql = captureDishesSelect(fn () => Livewire::test($component::class)->assertSee('Pastel de Nata'));

    // "name" is reserved by the visible column, so nothing is safely prunable -> select *.
    expect($sql)->toContain('select *');
});

it('keeps select * when pruneHiddenColumns is disabled', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-prune-disabled';

        public bool $pruneHiddenColumns = false;

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('additional');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Additional', 'additional')->searchable()->hidden(),
            ];
        }
    };

    $sql = captureDishesSelect(fn () => Livewire::test($component::class)->assertSee('Pastel de Nata'));

    expect($sql)->toContain('select *');
});

it('does not prune when the datasource has an explicit select', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-prune-explicit-select';

        public function datasource()
        {
            return Dish::query()->select(['id', 'name', 'additional']);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('additional');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Additional', 'additional')->searchable()->hidden(),
            ];
        }
    };

    $sql = captureDishesSelect(fn () => Livewire::test($component::class)->assertSee('Pastel de Nata'));

    // User's explicit projection is respected untouched.
    expect($sql)->toContain('"additional"');
});

it('does not prune when exporting so all columns remain available', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-prune-export';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('additional');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Additional', 'additional')->searchable()->hidden(),
            ];
        }
    };

    $instance = Livewire::test($component::class)->instance();

    $sql = captureDishesSelect(fn () => ProcessDataSource::make($instance)->get(isExport: true));

    // Export must keep the full projection so hidden+searchable data is exportable.
    expect($sql)->toContain('select *');
});
