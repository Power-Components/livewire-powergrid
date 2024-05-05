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
    PowerGridFields
};

class DishesCalculationsTable extends PowerGridComponent
{
    public array $eventId = [];

    public bool $join = false;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            Header::make()
                ->showToggleColumns()
                ->showSearchInput(),

            Footer::make()
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
            ->add('calories', fn ($dish) => $dish->calories . ' kcal')
            ->add('price');
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title(__('ID'))
                ->field('id')
                ->withCount('Count ID', true, false)
                ->sortable(),

            Column::add()
                ->title(__('Name'))
                ->searchable()
                ->field('name'),

            Column::make('Calories', 'calories', 'calories')
                ->withAvg('Average', header: true, footer: false)
                ->sortable(),

            Column::add()
                ->title(__('Price'))
                ->field('price')
                ->withSum('Sum Price', true, false)
                ->withCount('Count Price', true, false)
                ->withAvg('Avg Price', true, false)
                ->withMin('Min Price', true, false)
                ->withMax('Max Price', true, false),

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
            'price.{count}'  => fn ($value) => $fmt->format($value) . ' item(s)',
            'calories.{avg}' => fn ($value) => $fmt->format($value) . ' kcal',

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
