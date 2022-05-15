<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use NumberFormatter;
use PowerComponents\LivewirePowerGrid\Tests\Models\{Category, Dish};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent};

class DishesMakeTable extends PowerGridComponent
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
        return Dish::with('category');
    }

    public function relationSearch(): array
    {
        return [
            'category' => [
                'name',
            ],
        ];
    }

    public function inputRangeConfig(): array
    {
        return [
            'price' => ['thousands' => '.', 'decimal' => ','],
        ];
    }

    public function addColumns(): PowerGridEloquent
    {
        $fmt = new NumberFormatter('ca_ES', NumberFormatter::CURRENCY);

        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('storage_room')
            ->addColumn('chef_name')
            ->addColumn('serving_at')
            ->addColumn('calories')
            ->addColumn('calories', function (Dish $dish) {
                return $dish->calories . ' kcal';
            })
            ->addColumn('category_id', function (Dish $dish) {
                return $dish->category_id;
            })
            ->addColumn('category_name', function (Dish $dish) {
                return $dish->category->name;
            })
            ->addColumn('price')
            ->addColumn('price_EUR', function (Dish $dish) use ($fmt) {
                return $fmt->formatCurrency($dish->price, 'EUR');
            })
            ->addColumn('price_BRL', function (Dish $dish) {
                return 'R$ ' . number_format($dish->price, 2, ',', '.'); //R$ 1.000,00
            })
            ->addColumn('sales_price')
            ->addColumn('sales_price_BRL', function (Dish $dish) {
                $sales_price = $dish->price + ($dish->price * 0.15);

                return 'R$ ' . number_format($sales_price, 2, ',', '.'); //R$ 1.000,00
            })
            ->addColumn('in_stock')
            ->addColumn('in_stock_label', function (Dish $dish) {
                return ($dish->in_stock ? 'sim' : 'não');
            })
            ->addColumn('produced_at')
            ->addColumn('produced_at_formatted', function (Dish $dish) {
                return Carbon::parse($dish->produced_at)->format('d/m/Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Stored at', 'storage_room')
                ->sortable(),

            Column::make('Prato', 'name')
                ->searchable()
                ->editOnClick()
                ->clickToCopy(true)
                ->makeInputText('name')
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::make('Serving at', 'serving_at')
                ->sortable()
                ->makeInputSelect(Dish::servedAt(), 'serving_at', 'serving_at', ['live-search' => true]),

            Column::make('Chef', 'chef_name')
                ->searchable()
                ->editOnClick()
                ->clickToCopy(true)
                ->makeInputText('chef_name')
                ->placeholder('Chef placeholder')
                ->sortable(),

            Column::make('Categoria', 'category_name')
                ->placeholder('Categoria placeholder')
                ->makeInputSelect(Category::all(), 'name', 'category_id'),

            Column::make('Multiple', 'category_name')
                ->placeholder('Categoria')
                ->makeInputMultiSelect(Category::query()->take(5)->get(), 'name', 'category_id'),

            Column::make('Preço', 'price_BRL')
                ->editOnClick(true, 'price')
                ->makeInputRange('price'),

            Column::make('Preço de Venda', 'sales_price_BRL'),

            Column::make('Calorias', 'calories')
                ->makeInputRange('calories')
                ->sortable(),

            Column::make('Em Estoque', 'in_stock')
                ->toggleable(true, 'sim', 'não')
                ->makeBooleanFilter('in_stock', 'sim', 'não'),

            Column::make('Data de produção', 'produced_at_formatted')
                ->makeInputDatePicker('produced_at'),

            Column::make('Data', 'produced_at')
                ->makeInputDatePicker('produced_at')
                ->sortable(),
        ];
    }

    public function actions(): array
    {
        return [
            Button::make('edit-stock', '<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => 'id']),

            Button::make('destroy', 'Delete')
                ->class('text-center')
                ->emit('deletedEvent', ['dishId' => 'id'])
                ->method('delete'),
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
