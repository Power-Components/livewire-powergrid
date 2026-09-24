<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{PowerGrid, Rule};

it('selectCheckboxAll works properly', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-checkbox';

        public function datasource()
        {
            $data = [];
            for ($i = 1; $i <= 15; $i++) {
                $data[] = ['id' => $i, 'name' => 'Dish '.$i];
            }

            return collect($data);
        }

        public function setUp(): array
        {
            $this->showCheckBox();

            return [
                PowerGrid::footer()->showPerPage(10),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }
    };

    $lw = Livewire::test($component::class)
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    expect($lw->checkboxValues)
        ->toMatchArray(range(1, 10));

    $lw->call('nextPage')
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    expect($lw->checkboxValues)
        ->toMatchArray(range(1, 15));

    $lw->set('checkboxAll', false)
        ->call('selectCheckboxAll');

    expect($lw->checkboxValues)
        ->toBe([]);
});

it('resetToFirstPage does not navigate when already on the first page', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-reset-first-page';

        /** @var array<int, mixed> */
        public array $gotoPageCalls = [];

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1'],
                ['id' => 2, 'name' => 'Dish 2'],
            ]);
        }

        public function gotoPage($page, $pageName = 'page'): void
        {
            $this->gotoPageCalls[] = $page;

            parent::gotoPage($page, $pageName);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::footer()->showPerPage(10),
            ];
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

    $lw = Livewire::test($component::class);

    $lw->call('resetToFirstPage');
    expect($lw->get('gotoPageCalls'))->toBe([]);

    $lw->call('gotoPage', 3)
        ->call('resetToFirstPage');
    expect($lw->get('gotoPageCalls'))->toContain(1);
});

it('preserves the row selection when the per-page size changes', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-checkbox-perpage-change';

        public function datasource()
        {
            $data = [];
            for ($i = 1; $i <= 30; $i++) {
                $data[] = ['id' => $i, 'name' => 'Dish '.$i];
            }

            return collect($data);
        }

        public function setUp(): array
        {
            $this->showCheckBox();

            return [
                PowerGrid::footer()->showPerPage(10, [10, 30]),
            ];
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

    $lw = Livewire::test($component::class)
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    expect($lw->checkboxValues)->toMatchArray(range(1, 10));

    $lw->set('setUp.footer.perPage', 30);

    expect($lw->checkboxValues)->toMatchArray(range(1, 10));
});

it('selectCheckboxAll honours the explicit checked intent argument', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-checkbox-intent';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1'],
                ['id' => 2, 'name' => 'Dish 2'],
                ['id' => 3, 'name' => 'Dish 3'],
            ]);
        }

        public function setUp(): array
        {
            $this->showCheckBox();

            return [PowerGrid::footer()->showPerPage(10)];
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

    $lw = Livewire::test($component::class)
        ->call('selectCheckboxAll', true);

    expect($lw->get('checkboxAll'))->toBeTrue()
        ->and($lw->checkboxValues)->toMatchArray(['1', '2', '3']);

    $lw->call('selectCheckboxAll', false);

    expect($lw->get('checkboxAll'))->toBeFalse()
        ->and($lw->checkboxValues)->toBe([]);
});

it('selectCheckboxAll works properly with actionRules disable', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-checkbox-rules';

        public function datasource()
        {
            $data = [];
            for ($i = 1; $i <= 15; $i++) {
                $data[] = ['id' => $i, 'name' => 'Dish '.$i];
            }

            return collect($data);
        }

        public function setUp(): array
        {
            $this->showCheckBox();

            return [
                PowerGrid::footer()->showPerPage(10),
            ];
        }

        public function actionRules($row): array
        {
            return [
                Rule::checkbox()
                    ->when(fn ($dish) => in_array(data_get($dish, 'id'), [1, 2, 3]))
                    ->disable(),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }
    };

    $lw = Livewire::test($component::class)
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    // IDs 1, 2, 3 are disabled, so they shouldn't be selected from the first 10
    expect($lw->checkboxValues)
        ->toMatchArray(range(4, 10));

    $lw->call('nextPage')
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    // Adds 11, 12, 13, 14, 15
    expect($lw->checkboxValues)
        ->toMatchArray(range(4, 15));

    $lw->set('checkboxAll', false)
        ->call('selectCheckboxAll');

    expect($lw->checkboxValues)
        ->toBe([]);
});
