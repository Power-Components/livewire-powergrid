<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\Plugins\Export\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Plugins\Export\Export;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\Components\SetUp\Exportable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

it('keeps the openspout v5 export drivers available', function () {
    $drivers = config('livewire-powergrid.exportable');

    expect($drivers)->toHaveKey('openspout_v5')
        ->and($drivers)->not->toHaveKey('openspout_v4');

    foreach (['xlsx', 'csv'] as $type) {
        $class = data_get($drivers, "openspout_v5.$type");

        expect(class_exists($class))->toBeTrue("$class should exist")
            ->and(is_subclass_of($class, ExportInterface::class))->toBeTrue("$class should implement ExportInterface");
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

it('keeps the declared filter column when filters and export arrive in one request', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-export-csv-filter-shape';

        public function datasource()
        {
            return Dish::query()->where('id', 1);
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('export')->type(Exportable::TYPE_CSV)];
        }

        public function filters(): array
        {
            return [Filter::inputText('name')];
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

    $downloadedFile = Livewire::test($component::class)->update(
        calls: [['method' => 'exportToCsv', 'params' => [false], 'metadata' => []]],
        updates: ['filters' => ['name' => ['type' => 'select', 'value' => ['stored_at' => 'Pastel']]]],
    );

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

    $setUp = $this->value->instance()->resolvedSetUp();
    $filename = data_get($setUp, 'exportable.fileName').'.csv';
    $separator = data_get($setUp, 'exportable.csvSeparator', ',');
    $delimiter = data_get($setUp, 'exportable.csvDelimiter', '"');

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

it('uses the exporter mapped in config for synchronous exports', function () {
    $exporter = new class() extends Export implements ExportInterface
    {
        public static array $exportedNames = [];

        public function download(array $exportOptions): BinaryFileResponse
        {
            self::$exportedNames = collect($this->data)->pluck('name')->all();

            file_put_contents($path = storage_path($this->fileName.'.csv'), 'custom');

            return response()->download($path)->deleteFileAfterSend();
        }

        public function store(array $exportOptions): void {}

        public function build(Exportable|array $exportOptions): void {}
    };

    config(['livewire-powergrid.exportable.openspout_v5.csv' => $exporter::class]);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-export-custom-exporter';

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

    Livewire::test($component::class)->call('exportToCsv', false);

    expect($exporter::$exportedNames)->toBe(['Pastel de Nata']);
});
