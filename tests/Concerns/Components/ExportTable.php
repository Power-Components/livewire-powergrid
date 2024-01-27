<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{
    Column,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridFields,
    Traits\WithExport
};

class ExportTable extends PowerGridComponent
{
    use WithExport;

    public string $separator = ',';

    public string $delimiter = '"';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->csvSeparator($this->separator)
                ->csvDelimiter($this->delimiter)
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            Header::make()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Dish::with('category');
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
            Column::add()
                ->title(__('ID'))
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title(__('Prato'))
                ->field('name')
                ->searchable()
                ->placeholder('Prato placeholder')
                ->sortable(),
        ];
    }

    public function bootstrap()
    {
        config(['livewire-powergrid.theme' => 'bootstrap']);
    }

    public function tailwind()
    {
        config(['livewire-powergrid.theme' => 'tailwind']);
    }
}
