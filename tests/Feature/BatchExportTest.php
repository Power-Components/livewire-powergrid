<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

use function Livewire\invade;

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
