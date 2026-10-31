<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\{Bus, Storage};
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\BatchExportTable;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\Components\SetUp\Exportable;

use function Livewire\invade;
use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('can pass class parameters in batch export', function () {
    Bus::fake();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-batch-export';

        public int $filterDataSourceId = 77;

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('export')->type(Exportable::TYPE_XLS)->queues(1)];
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

    $lw = Livewire::test($component::class, ['filterDataSourceId' => 77])
        ->call('exportToXLS', false);

    Bus::assertBatched(function (PendingBatch $batch) {
        $properties = invade($batch->jobs[0])->properties;

        return $properties['filterDataSourceId'] === 77;
    });
})->requiresOpenSpout();

it('prevents the client from overwriting export state', function () {
    expect(function () {
        livewire(BatchExportTable::class, ['filterDataSourceId' => 1])
            ->set('exportState', ['files' => ['file.xlsx']]);
    })->toThrow(CannotUpdateLockedPropertyException::class);
});

it('does not download a file that is not part of the export', function (string $path) {
    $component = livewire(BatchExportTable::class, ['filterDataSourceId' => 1])
        ->call('downloadExport', $path);

    expect(data_get($component->effects, 'download'))->toBeNull();
})->with([
    'parent' => ['../up.txt'],
    'grandparent' => ['../../up-two.txt'],
    'absolute' => ['/absolute/path.txt'],
    'nested' => ['a/../../b.txt'],
]);

it('does not download a file that exists in storage but was not exported', function () {
    $file = 'other-file.txt';
    $storagePath = storage_path($file);

    file_put_contents($storagePath, 'data');

    try {
        $component = livewire(BatchExportTable::class, ['filterDataSourceId' => 1])
            ->call('downloadExport', $file);

        expect(data_get($component->effects, 'download'))->toBeNull();
    } finally {
        @unlink($storagePath);
    }
});

it('does not download a path that is not a plain filename, even when it is listed', function (string $relative) {
    $component = livewire(BatchExportTable::class, [
        'filterDataSourceId' => 1,
        'exportState' => ['files' => [$relative]],
    ])->call('downloadExport', $relative);

    expect(data_get($component->effects, 'download'))->toBeNull();
})->with([
    'subdirectory' => ['sub/other-file.txt'],
    'parent' => ['../other-file.txt'],
]);

it('downloads a file produced by the export', function () {
    $file = 'testing-batch-export-1-1-fake.xlsx';

    Storage::disk('local')->put($file, 'dummy');

    try {
        livewire(BatchExportTable::class, [
            'filterDataSourceId' => 1,
            'exportState' => ['files' => [$file]],
        ])
            ->call('downloadExport', $file)
            ->assertFileDownloaded($file);
    } finally {
        Storage::disk('local')->delete($file);
    }
});

it('can configure disk and directory in batch export', function () {
    Bus::fake();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-batch-export-storage';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::exportable('export')
                    ->type(Exportable::TYPE_XLS)
                    ->queues(1)
                    ->disk('s3')
                    ->directory('exports/'),
            ];
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
        ->call('exportToXLS', false);

    Bus::assertBatched(function (PendingBatch $batch) {
        $exportable = invade($batch->jobs[0])->exportable;

        return data_get($exportable, 'disk') === 's3' && data_get($exportable, 'directory') === 'exports/';
    });
})->requiresOpenSpout();

it('dispatches the export chunks as parallel batch jobs (not a chain)', function () {
    Bus::fake();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-batch-parallel';

        public function datasource()
        {
            return collect(range(1, 10))->map(fn ($i) => ['id' => $i, 'name' => "Dish $i"]);
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('export')->type(Exportable::TYPE_XLS)->queues(3)];
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

    Livewire::test($component::class)->call('exportToXLS', false);

    Bus::assertBatched(function (PendingBatch $batch) {
        // Three independent (parallel) jobs at the top level, not a single nested chain.
        return count($batch->jobs) === 3
            && ! is_array($batch->jobs[0]);
    });
})->requiresOpenSpout();

it('keeps hidden columns out of queued exports after toggling columns', function () {
    Bus::fake();
    Storage::fake('local');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-batch-hidden-column';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1', 'calories' => 9876]]);
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('export')->type(Exportable::TYPE_CSV)->queues(1)];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('calories');
        }

        public function columns(): array
        {
            return [
                Column::make('ID', 'id'),
                Column::make('Name', 'name'),
                Column::make('Calories', 'calories')->hidden(),
            ];
        }
    };

    Livewire::test($component::class)->update(calls: [
        ['method' => 'toggleColumn', 'params' => ['calories'], 'path' => ''],
        ['method' => 'exportToCsv', 'params' => [false], 'path' => ''],
    ]);

    Bus::assertBatched(function (PendingBatch $batch) {
        $batch->jobs[0]->handle();

        return true;
    });

    $content = collect(Storage::disk('local')->allFiles())
        ->map(fn (string $file) => Storage::disk('local')->get($file))
        ->implode('');

    expect($content)->toContain('Dish 1')->not->toContain('9876');
})->requiresOpenSpout();

it('renders the user-supplied progressView below the header', function () {
    view()->addNamespace('pg-test-views', __DIR__.'/../Concerns/views');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-batch-progress';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1'], ['id' => 2, 'name' => 'Dish 2']]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::exportable('export')
                    ->type(Exportable::TYPE_XLS)
                    ->queues(2)
                    ->progressView('pg-test-views::export-progress'),
            ];
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

    $html = Livewire::test($component::class)->html();

    expect($html)->toContain('my-custom-export-progress');
});

it('renders no progress panel when no progressView is set', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-batch-no-progress';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('export')->type(Exportable::TYPE_XLS)->queues(2)];
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

    $html = Livewire::test($component::class)->html();

    expect($html)->toContain('pg-header-export')          // dropdown present
        ->and($html)->not->toContain('my-custom-export-progress');
});

function scopedBatchExportComponent(): string
{
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-batch-scope';

        public function datasource()
        {
            return Dish::query();
        }

        public function transformQuery(mixed $query): mixed
        {
            return $query->where('name', 'like', 'Pizza%');
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('scoped')->type(Exportable::TYPE_CSV)->queues(1)];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('ID', 'id'), Column::make('Name', 'name')->searchable()];
        }
    };

    return $component::class;
}

it('applies transformQuery and grid state inside the queued export job', function () {
    Bus::fake();
    Storage::fake('local');
    Dish::query()->forceDelete();

    foreach (['Pizza Margherita', 'Pizza Calabresa', 'Sushi'] as $name) {
        Dish::create(['name' => $name, 'price' => 10, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 100, 'stored_at' => '1', 'produced_at' => now()]);
    }

    Livewire::test(scopedBatchExportComponent())
        ->set('search', 'Calabresa')
        ->call('exportToCsv', false);

    $job = null;
    Bus::assertBatched(function (PendingBatch $batch) use (&$job) {
        $job = $batch->jobs[0];

        return true;
    });

    $job->handle();

    $csv = Storage::disk('local')->get(invade($job)->fileName);

    expect($csv)->toContain('Pizza Calabresa')
        ->not->toContain('Pizza Margherita')
        ->not->toContain('Sushi');
})->requiresOpenSpout();

it('does not start a second queued export while one is running', function () {
    Bus::fake();

    Livewire::test(scopedBatchExportComponent())
        ->call('exportToCsv', false)
        ->call('exportToCsv', false);

    Bus::assertBatchCount(1);
})->requiresOpenSpout();
