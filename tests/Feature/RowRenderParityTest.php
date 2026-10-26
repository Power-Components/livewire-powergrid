<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

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
        ->assertSeeHtml('toggleExpanded('.js_literal(1).')');
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

it('applies the row value of contentClassField as the cell class', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'parity-content-class-field';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'status_class' => 'text-green-600'],
                ['id' => 2, 'name' => 'Dish 2', 'status_class' => '"><b>x</b>'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('status_class');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name')
                    ->contentClassField('status_class')
                    ->contentClasses('font-bold'),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('<span class="text-green-600 font-bold">')
        ->assertSeeHtml('<span class="&quot;&gt;&lt;b&gt;x&lt;/b&gt; font-bold">')
        ->assertDontSeeHtml('class="status_class');
});
