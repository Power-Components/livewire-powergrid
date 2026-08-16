<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;
use PowerComponents\Turbine\Components\SetUp\Exportable;

it('renders export dropdown with xlsx button', function () {
    config(['livewire-powergrid.theme' => Tailwind::class]);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-dropdown';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish 1']]);
        }

        public function setUp(): array
        {
            return [PowerGrid::exportable('export')->type(Exportable::TYPE_XLS)];
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

    $lw = Livewire::test($component::class);
    $html = $lw->html();
    expect($html)->toContain('pg-header-export')
        ->and($html)->toContain('exportToXLS')
        ->and($html)->toContain('x-data="pgExport"')
        ->and($html)->toContain('openMenu()');

    $lw->call('fetchDatasource');
    $html2 = $lw->html();
    expect($html2)->toContain('pg-header-export')
        ->and($html2)->toContain('exportToXLS')
        ->and($html2)->toContain('x-data="pgExport"')
        ->and($html2)->toContain('openMenu()');
});
