<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

uses()->group('summaries');

it('formats a summary value through summarizeFormat', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'summary-format';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'A', 'price' => 10],
                ['id' => 2, 'name' => 'B', 'price' => 40],
            ]);
        }

        public function summarizeFormat(): array
        {
            return [
                'price.{sum}' => fn ($value) => 'TOTAL::'.$value,
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
                Column::make('Price', 'price')->withSum('Sum', true, true),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertOk()
        ->assertSee('TOTAL::50');
});

it('builds the summaries cache tag from a custom tag', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'summary-cache-tag';

        public function setUp(): array
        {
            return [
                PowerGrid::cache()->customTag('my-custom-tag')->prefix('turbine_'),
            ];
        }

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'A', 'price' => 10],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Price', 'price')->withSum('Sum', true, true),
            ];
        }
    };

    $test = Livewire::test($component::class)->assertOk();

    expect($test->instance()->summariesCacheTag())->toBe('turbine_my-custom-tag');
});

it('builds the summaries cache tag from the datasource table when no custom tag is set', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'summary-cache-default';

        public function datasource()
        {
            return collect([['id' => 1, 'price' => 10]]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('price');
        }

        public function columns(): array
        {
            return [Column::make('Price', 'price')->withSum('Sum', true, true)];
        }
    };

    $test = Livewire::test($component::class);

    // no getModel() on a collection → falls back to the table name
    expect($test->instance()->summariesCacheTag())->toContain('summary-cache-default');
});
