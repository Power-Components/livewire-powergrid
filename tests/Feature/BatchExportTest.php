<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields, Traits\WithExport};
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

use function Livewire\invade;

it('can pass class parameters in batch export', function () {
    Bus::fake();

    $component = new class() extends PowerGridComponent
    {
        use WithExport;

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
        $jobs = $batch->jobs[0];
        $properties = invade($jobs[0])->properties;

        return $properties['filterDataSourceId'] === 77;
    });
})->requiresOpenSpout();
