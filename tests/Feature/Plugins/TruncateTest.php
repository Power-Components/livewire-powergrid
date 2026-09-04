<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('truncates the column value with an ellipsis', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-truncate-limit';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'A very long dish name']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->limit(6),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('A very...')
        ->assertDontSee('A very long dish name');
});

it('does not render a tooltip outside the flux theme', function () {
    // The tooltip is opt-in per theme (only Flux ships the view). Under the
    // default theme, ->tooltip() must not leak the full value into the markup.
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-truncate-tooltip-default';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'A very long dish name']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->limit(6)->tooltip(),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('A very...')
        ->assertDontSee('A very long dish name');
});

it('does not truncate nor tooltip when the value is shorter than the limit', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-truncate-short';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Short']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->limit(50)->tooltip(),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('Short')
        ->assertDontSee('...');
});

it('does not truncate when no limit is set', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-truncate-off';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'A very long dish name']]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('A very long dish name');
});
