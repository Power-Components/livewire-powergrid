<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Plugins\Export\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

it('keeps both openspout v4 and v5 export drivers available', function () {
    $drivers = config('livewire-powergrid.exportable');

    expect($drivers)->toHaveKeys(['openspout_v4', 'openspout_v5']);

    foreach (['openspout_v4', 'openspout_v5'] as $driver) {
        foreach (['xlsx', 'csv'] as $type) {
            $class = data_get($drivers, "$driver.$type");

            expect(class_exists($class))->toBeTrue("$class should exist")
                ->and(is_subclass_of($class, ExportInterface::class))->toBeTrue("$class should implement ExportInterface");
        }
    }
});

it('properly export xls - all data', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-export-xls';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('export')->type(Exportable::TYPE_XLS)];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('ID', 'id'), Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->call('exportToXLS', false)
        ->assertFileDownloaded('export.xlsx');
})->requiresOpenSpout();

it('properly export csv data with selected data', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-export-csv';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel de Nata'],
                ['id' => 2, 'name' => 'Peixada'],
            ]);
        }

        public function setUp(): array
        {
            $this->showCheckBox();

            return [PowerGrid::exportable('export')->type(Exportable::TYPE_CSV)];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('ID', 'id'), Column::make('Name', 'name')];
        }
    };

    $downloadedFile = Livewire::test($component::class)
        ->set('checkboxValues', ['1', '2'])
        ->call('exportToCsv', true);

    $headings = ['ID', 'Name'];
    $rows = [['1', 'Pastel de Nata'], ['2', 'Peixada']];

    expect($downloadedFile)->toBeCsvDownload($headings, $rows);
})->requiresOpenSpout();

it('streams a database datasource to csv with correct content', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-export-csv-db';

        public function datasource()
        {
            return Dish::query()->where('id', 1);
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('export')->type(Exportable::TYPE_CSV)];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('ID', 'id'), Column::make('Name', 'name')];
        }
    };

    $downloadedFile = Livewire::test($component::class)
        ->call('exportToCsv', false);

    expect($downloadedFile)->toBeCsvDownload(['ID', 'Name'], [['1', 'Pastel de Nata']]);
})->requiresOpenSpout();

/*
|--------------------------------------------------------------------------
| Expectations for this test
|--------------------------------------------------------------------------
|
*/

expect()->extend('toBeCsvDownload', function (array $headings, array $rows) {
    $downloadEffect = data_get($this->value->effects, 'download');

    $filename = data_get($this->value->setUp, 'exportable.fileName').'.csv';
    $separator = data_get($this->value->setUp, 'exportable.csvSeparator', ',');
    $delimiter = data_get($this->value->setUp, 'exportable.csvDelimiter', '"');

    test()->assertEquals(
        $filename,
        data_get($downloadEffect, 'name')
    );

    $content = str_replace(["\r\n", "\n", "\r"], '<csv-divider>', base64_decode(data_get($downloadEffect, 'content')));

    $expected = collect(array_merge([$headings], $rows))
        ->transform(function ($heading) use ($delimiter, $separator) {
            $heading = collect($heading)->transform(fn ($heading) => trim($heading, $delimiter))->toArray();

            return implode($separator, $heading);
        });

    $actual = collect(explode('<csv-divider>', $content))
        ->filter(fn ($heading) => strlen($heading) > 0)
        ->transform(function ($line) use ($delimiter, $separator) {
            $arrayLine = explode($separator, preg_replace('/[^A-Za-z0-9.!?|@,á" ]/', '', $line ?? ''));

            $rows = collect($arrayLine)
                ->transform(fn ($row) => trim($row, $delimiter))
                ->toArray();

            return implode($separator, $rows);
        });

    test()->assertEquals(
        $expected,
        $actual
    );
});
