<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use NumberFormatter;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{
    Button,
    Column,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridColumns,
    PowerGridComponent
};

class DishesQueryBuilderTable extends PowerGridComponent
{
    use ActionButton;

    public array $eventId = [];

    public array $testFilters = [];

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'deletedEvent',
            ]
        );
    }

    public function openModal(array $params)
    {
        $this->eventId = $params;
    }

    public function deletedEvent(array $params)
    {
        $this->eventId = $params;
    }

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
        return DB::table('dishes')
            ->join('categories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'categories.id');
            })
            ->select('dishes.*', 'categories.name as category_name');
    }

    public function relationSearch(): array
    {
        return [
            'category' => [
                'name',
            ],
        ];
    }

    public function addColumns(): PowerGridColumns
    {
        $fmt = new NumberFormatter('ca_ES', NumberFormatter::CURRENCY);

        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('storage_room')
            ->addColumn('chef_name')
            ->addColumn('serving_at')
            ->addColumn('calories')
            ->addColumn('calories', function ($dish) {
                return $dish->calories . ' kcal';
            })
            ->addColumn('category_id', function ($dish) {
                return $dish->category_id;
            })
            ->addColumn('category_name')
            ->addColumn('price')
            ->addColumn('price_EUR', function ($dish) use ($fmt) {
                return $fmt->formatCurrency($dish->price, 'EUR');
            })
            ->addColumn('price_BRL', function ($dish) {
                return 'R$ ' . number_format($dish->price, 2, ',', '.'); //R$ 1.000,00
            })
            ->addColumn('sales_price')
            ->addColumn('sales_price_BRL', function ($dish) {
                $sales_price = $dish->price + ($dish->price * 0.15);

                return 'R$ ' . number_format($sales_price, 2, ',', '.'); //R$ 1.000,00
            })
            ->addColumn('in_stock')
            ->addColumn('in_stock_label', function ($dish) {
                return ($dish->in_stock ? 'sim' : 'não');
            })
            ->addColumn('produced_at')
            ->addColumn('produced_at_formatted', function ($dish) {
                return Carbon::parse($dish->produced_at)->format('d/m/Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title('ID')
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Stored at')
                ->field('storage_room')
                ->sortable(),

            Column::add()
                ->title('Dish')
                ->field('name')
                ->searchable()
                ->editOnClick()
                ->clickToCopy(true)
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::add()
                ->title('Serving at')
                ->field('serving_at')
                ->sortable(),

            Column::add()
                ->title('Chef')
                ->field('chef_name')
                ->searchable()
                ->editOnClick()
                ->clickToCopy(true)
                ->placeholder('Chef placeholder')
                ->sortable(),

            Column::add()
                ->title('Category')
                ->field('category_name')
                ->placeholder('Category placeholder'),

            Column::add()
                ->title('Price')
                ->field('price_BRL')
                ->editOnClick(true, 'price'),

            Column::add()
                ->title('Sales Price')
                ->field('sales_price_BRL'),

            Column::add()
                ->title('Calories')
                ->field('calories')
                ->sortable(),

            Column::add()
                ->title('In Stock')
                ->toggleable(true, 'sim', 'não')
                ->field('in_stock'),

            Column::add()
                ->title('Produced At')
                ->field('produced_at_formatted'),

            Column::add()
                ->title(__('Data'))
                ->field('produced_at')
                ->sortable(),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('edit-stock')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => 'id']),

            Button::add('destroy')
                ->caption(__('Delete'))
                ->class('text-center')
                ->emit('deletedEvent', ['dishId' => 'id'])
                ->method('delete'),
        ];
    }

    public function filters(): array
    {
        return $this->testFilters;
    }

    public function bootstrap(): void
    {
        config(['livewire-powergrid.theme' => 'bootstrap']);
    }

    public function tailwind(): void
    {
        config(['livewire-powergrid.theme' => 'tailwind']);
    }
}
