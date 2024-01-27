<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{
    Column,
    Exportable,
    Facades\Rule,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridFields
};

class DishesTableWithJoin extends PowerGridComponent
{
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

    public string $sortField = 'dishes.id';

    public string $primaryKey = 'dishes.id';

    public bool $withSortStringNumber = true;

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

    public function dataSource(): Builder
    {
        return Dish::query()
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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('dish_name', function (Dish $dish) {
                return $dish->name;
            })
            ->add('calories')
            ->add('serving_at')
            ->add('calories', function (Dish $dish) {
                return $dish->calories . ' kcal';
            })
            /*** CATEGORY ***/
            ->add('category_id', function (Dish $dish) {
                return $dish->category_id;
            })
            ->add('category_name', function (Dish $dish) {
                return $dish->category->name;
            })
            /*** PRICE ***/
            ->add('price')
            ->add('price_BRL', function (Dish $dish) {
                return 'R$ ' . number_format($dish->price, 2, ',', '.'); //R$ 1.000,00
            })
            /*** SALE'S PRICE ***/
            ->add('sales_price')
            ->add('sales_price_BRL', function (Dish $dish) {
                $sales_price = $dish->price + ($dish->price * 0.15);

                return 'R$ ' . number_format($sales_price, 2, ',', '.'); //R$ 1.000,00
            })
            /*** STOCK ***/
            ->add('in_stock')
            ->add('in_stock_label', function (Dish $dish) {
                return ($dish->in_stock ? 'sim' : 'não');
            })
            /*** Produced At ***/
            ->add('created_at')
            ->add('created_at_formatted', function (Dish $dish) {
                return \Carbon\Carbon::parse($dish->category->created_at)->format('d/m/Y');
            })
            /*** Produced At ***/
            ->add('produced_at')
            ->add('produced_at_formatted', function (Dish $dish) {
                return Carbon::parse($dish->produced_at)->format('d/m/Y');
            });
    }

    public function columns(): array
    {
        $canEdit = true;

        return [
            Column::add()
                ->title(__('ID'))
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title(__('Prato'))
                ->field('dish_name')
                ->searchable()
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::add()
                ->title('Serving at')
                ->field('serving_at')
                ->sortable(),

            Column::add()
                ->title(__('Category'))
                ->field('category_name', 'categories.name')
                ->sortable()
                ->placeholder('Category placeholder'),

            Column::add()
                ->title(__('Preço'))
                ->field('price_BRL'),

            Column::add()
                ->title(__('Preço de Venda'))
                ->field('sales_price_BRL'),

            Column::add()
                ->title(__('Calorias'))
                ->field('calories')
                ->sortable(),

            Column::add()
                ->title(__('Em Estoque'))
                ->toggleable(true, 'sim', 'não')
                ->sortable()
                ->field('in_stock'),

            Column::add()
                ->title(__('Created Categories'))
                ->sortable()
                ->field('created_at_formatted', 'categories.created_at'),

            Column::add()
                ->title(__('Data de produção'))
                ->field('produced_at_formatted'),

            Column::action('Action'),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 2)
                ->hide(),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 4)
                ->slot('cation edit for id 4'),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => (bool) $dish->in_stock === false && $dish->id !== 8)
                ->redirect(fn ($dish) => 'https://www.dish.test/sorry-out-of-stock?dish=' . $dish->id),

            // Set a row red background for when dish is out of stock
            Rule::rows()
                ->when(fn ($dish) => (bool) $dish->in_stock === false)
                ->setAttribute('class', 'bg-red-100 text-red-800'),

            Rule::rows()
                ->when(fn ($dish) => $dish->id == 3)
                ->setAttribute('class', 'bg-pg-secondary-100'),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 5)
                ->dispatch('toggleEvent', ['dishId' => 'id']),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 9)
                ->disable(),
        ];
    }

    public function filters(): array
    {
        return $this->testFilters;
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
