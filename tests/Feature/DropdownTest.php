<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

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
        ->and($html)->toContain('open = true');

    $lw->call('fetchDatasource');
    $html2 = $lw->html();
    expect($html2)->toContain('pg-header-export')
        ->and($html2)->toContain('exportToXLS')
        ->and($html2)->toContain('open = true');
});
