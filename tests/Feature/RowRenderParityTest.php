<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('renders the detail collapse-icon toggle', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'parity-detail';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::detail()
                    ->view('livewire-powergrid::tests.detail')
                    ->showCollapseIcon(),
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
        ->assertOk()
        ->assertSeeHtml('data-table-name="parity-detail"')
        ->assertSeeHtml('data-row-id="1"');
});

it('renders the responsive toggle', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'parity-responsive';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [PowerGrid::responsive()];
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
        ->assertOk()
        ->assertSeeHtml("toggleExpanded('1')");
});

it('renders the radio column', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'parity-radio';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            $this->showRadioButton('id');

            return [];
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
        ->assertOk()
        ->assertSeeHtml('wire:key="radio-row-1"');
});

it('renders the checkbox column', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'parity-checkbox';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            $this->showCheckBox('id');

            return [];
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
        ->assertOk()
        ->assertSeeHtml('wire:key="checkbox-row-1"');
});
