<?php

use Illuminate\Support\Collection;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\Themes\{Flux, Tailwind, Theme};

it('boots the theme and sets the bound instance on powergrid component', function () {
    config()->set('livewire-powergrid.theme', Tailwind::class);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-table';

        public string $primaryKey = 'id';

        public function setUp(): array
        {
            return [];
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }

        public function datasource(): ?Collection
        {
            return collect();
        }

        public function template(): ?Theme
        {
            return Tailwind::make()->merge([
                'table' => [
                    'base' => 'test-table-base-class',
                ],
            ]);
        }
    };

    Livewire::test($component::class)
        ->assertSeeHtml('test-table-base-class');

    /** @var Theme $boundTheme */
    $boundTheme = app('powergrid.theme');

    expect($boundTheme)->toBeInstanceOf(Tailwind::class)
        ->and($boundTheme->get('table.base'))->toBe('test-table-base-class');
});

it('boots the theme using customThemeClass from component', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-table';

        public string $primaryKey = 'id';

        public function setUp(): array
        {
            return [];
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
            ];
        }

        public function datasource(): ?Collection
        {
            return collect();
        }

        public function customThemeClass(): ?string
        {
            return Flux::class;
        }
    };

    Livewire::test($component::class);

    $boundTheme = app('powergrid.theme');

    expect($boundTheme)->toBeInstanceOf(Flux::class);
});
