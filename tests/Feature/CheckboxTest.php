<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Rule;

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
