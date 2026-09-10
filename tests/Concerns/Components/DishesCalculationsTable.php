<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\Column;
use PowerComponents\Turbine\Components\SetUp\Exportable;

class DishesCalculationsTable extends PowerGridComponent
{
    public string $tableName = 'testing-dishes-calculations-table';

    public array $eventId = [];

    public bool $join = false;

    public function start(): void
    {
        PowerGrid::start()
            ->summarize();
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showToggleColumns()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        if ($this->join) {
            return $this->join();
        }

        return $this->query();
    }

    public function query(): Builder
    {
        return Dish::with('category');
    }

    public function join(): Builder
    {
        return Dish::query()
            ->join('categories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'categories.id');
            })
            ->select('dishes.*', 'categories.name as category_name');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('calories', fn ($dish) => $dish->calories.' kcal')
            ->add('price');
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title(__('ID'))
                ->field('id')
                ->summarize('count', 'Count ID', true, false)
                ->sortable(),

            Column::add()
                ->title(__('Name'))
                ->searchable()
                ->field('name'),

            Column::make('Calories', 'calories', 'calories')
                ->summarize('avg', 'Average', header: true, footer: false)
                ->sortable(),

            Column::add()
                ->title(__('Price'))
                ->field('price')
                ->summarize('sum', 'Sum Price', true, false)
                ->summarize('count', 'Count Price', true, false)
                ->summarize('avg', 'Avg Price', true, false)
                ->summarize('min', 'Min Price', true, false)
                ->summarize('max', 'Max Price', true, false),

            Column::action('Action'),
        ];
    }

    public function summarizeFormat(): array
    {
        $fmt = (new \NumberFormatter('pt-PT', \NumberFormatter::DEFAULT_STYLE));

        return [
            'price.{sum,avg,min,max}' => function ($value) {
                return (new \NumberFormatter('en_US', \NumberFormatter::CURRENCY))
                    ->formatCurrency($value, 'USD');
            },
            'price.{count}' => fn ($value) => $fmt->format($value).' item(s)',
            'calories.{avg}' => fn ($value) => $fmt->format($value).' kcal',

        ];
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
}
