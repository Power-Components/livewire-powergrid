<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\{Carbon, HtmlString};
use PowerComponents\LivewirePowerGrid\Tests\Models\{Category, Dish};
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent,
    Rules\Rule,
    Traits\ActionButton};

class DishesTableWithJoin extends PowerGridComponent
{
    use ActionButton;

    public array $eventId = [];

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

    public function setUp()
    {
        $this->showCheckBox()
            ->showPerPage()
            ->showRecordCount()
            ->showExportOption('download', ['excel', 'csv'])
            ->showSearchInput();
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

    public function addColumns(): ?PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('dish_name', function (Dish $dish) {
                return $dish->name;
            })
            ->addColumn('calories')
            ->addColumn('calories', function (Dish $dish) {
                return $dish->calories . ' kcal';
            })
            /*** CATEGORY ***/
            ->addColumn('category_id', function (Dish $dish) {
                return $dish->category_id;
            })
            ->addColumn('category_name', function (Dish $dish) {
                return $dish->category->name;
            })
            /*** PRICE ***/
            ->addColumn('price')
            ->addColumn('price_BRL', function (Dish $dish) {
                return 'R$ ' . number_format($dish->price, 2, ',', '.'); //R$ 1.000,00
            })
            /*** SALE'S PRICE ***/
            ->addColumn('sales_price')
            ->addColumn('sales_price_BRL', function (Dish $dish) {
                $sales_price = $dish->price + ($dish->price * 0.15);

                return 'R$ ' . number_format($sales_price, 2, ',', '.'); //R$ 1.000,00
            })
            /*** STOCK ***/
            ->addColumn('in_stock')
            ->addColumn('in_stock_label', function (Dish $dish) {
                return ($dish->in_stock ? 'sim' : 'não');
            })
            /*** Produced At ***/
            ->addColumn('created_at')
            ->addColumn('created_at_formatted', function (Dish $dish) {
                return \Carbon\Carbon::parse($dish->category->created_at)->format('d/m/Y');
            })
            /*** Produced At ***/
            ->addColumn('produced_at')
            ->addColumn('produced_at_formatted', function (Dish $dish) {
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
                ->withCount('Count ID', false, true)
                ->searchable()
                ->sortable(),

            Column::add()
                ->title(__('Prato'))
                ->field('dish_name')
                ->searchable()
                ->clickToCopy(true)
                ->makeInputText('name')
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::add()
                ->title(__('Categoria'))
                ->field('category_name', 'categories.name')
                ->sortable()
                ->placeholder('Categoria placeholder')
                ->makeInputSelect(Category::all(), 'name', 'category_id'),

            Column::add()
                ->title(__('Preço'))
                ->field('price_BRL')
                ->withSum('Sum Price', false, true)
                ->withCount('Count', false, true)
                ->withAvg('Avg Price', false, true)
                ->editOnClick($canEdit)
                ->makeInputRange('price', '.', ','),

            Column::add()
                ->title(__('Preço de Venda'))
                ->field('sales_price_BRL'),

            Column::add()
                ->title(__('Calorias'))
                ->field('calories')
                ->makeInputRange('calories')
                ->sortable(),

            Column::add()
                ->title(__('Em Estoque'))
                ->toggleable(true, 'sim', 'não')
                ->headerAttribute('', 'width: 100px;')
                ->makeBooleanFilter('in_stock', 'sim', 'não')
                ->sortable()
                ->field('in_stock'),

            Column::add()
                ->title(__('Created Categories'))
                ->sortable()
                ->field('created_at_formatted', 'categories.created_at')
                ->makeInputDatePicker('categories.created_at'),

            Column::add()
                ->title(__('Data de produção'))
                ->field('produced_at_formatted')
                ->makeInputDatePicker('produced_at'),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('edit-stock')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => 'id']),

            Button::add('edit-stock-for-rules')
                ->caption('<div id="edit">Edit for Rules</div>')
                ->class('text-center')
                ->openModal('edit-stock-for-rules', ['dishId' => 'id']),

            Button::add('destroy')
                ->caption(__('Delete'))
                ->class('text-center')
                ->emit('deletedEvent', ['dishId' => 'id'])
                ->method('delete'),

            Button::add('destroy-for-emit-to')
                ->caption(__('Delete'))
                ->class('text-center')
                ->emitTo('dishes-table', 'deletedEvent', ['dishId' => 'id'])
                ->method('delete'),
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
                ->caption('cation edit for id 4'),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish)     => (bool) $dish->in_stock === false && $dish->id !== 8)
                ->redirect(fn ($dish) => 'https://www.dish.test/sorry-out-of-stock?dish=' . $dish->id),

            // Set a row red background for when dish is out of stock
            Rule::rows()
                ->when(fn ($dish) => (bool) $dish->in_stock === false)
                ->setAttribute('class', 'bg-red-100 text-red-800'),

            Rule::rows()
                ->when(fn ($dish) => $dish->id == 3)
                ->setAttribute('class', 'bg-blue-100'),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 5)
                ->emit('toggleEvent', ['dishId' => 'id']),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 9)
                ->disable(),
        ];
    }

    public function update(array $data): bool
    {
        try {
            $updated = Dish::query()->find($data['id'])->update([
                $data['field'] => $data['value'],
            ]);
        } catch (QueryException $exception) {
            $updated = false;
        }

        return $updated;
    }

    public function updateMessages(string $status, string $field = '_default_message'): string
    {
        $updateMessages = [
            'success' => [
                '_default_message' => __('Data has been updated successfully!'),
                'price_BRL'        => __('Preço alterado'),
            ],
            'error' => [
                '_default_message' => __('Error updating the data.'),
            ],
        ];

        return ($updateMessages[$status][$field] ?? $updateMessages[$status]['_default_message']);
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
