<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('dispatches event to toggle detail row', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-detail-row';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1'],
            ]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::detail()
                    ->view('livewire-powergrid::tests.detail'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->assertDontSee('Id 1')
        ->assertDontSee('livewire:powergrid-detail')
        ->call('toggleDetail', '1')
        ->assertSee('Id 1')
        ->assertSet('openedDetailIds', ['1' => true])
        ->call('toggleDetail', '1')
        ->assertDontSee('Id 1')
        ->assertSet('openedDetailIds', []);
});

it('keeps a single detail row open when singleExpand is set', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-detail-single';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1'],
                ['id' => 2, 'name' => 'Dish 2'],
            ]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::detail()
                    ->view('livewire-powergrid::tests.detail')
                    ->singleExpand(),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->call('toggleDetail', '1')
        ->assertSee('Id 1')
        ->assertDontSee('Id 2')
        ->call('toggleDetail', '2')
        ->assertDontSee('Id 1')
        ->assertSee('Id 2')
        ->assertSet('openedDetailIds', ['2' => true]);
});

it('registers tbody partials after sort when detail is enabled', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-detail-sort';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1'],
            ]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::detail()
                    ->view('livewire-powergrid::tests.detail'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    $test = Livewire::test($component::class)
        ->call('toggleDetail', '1')
        ->call('sortBy', 'name');

    $fragments = \Livewire\store($test->instance())->get('partialFragments') ?? [];

    $names = [];

    foreach ($fragments as $renderUsing) {
        $names = array_merge($names, array_keys($renderUsing()));
    }

    expect($names)->toContain('pg-tbody-'.$test->instance()->tableName);
});
