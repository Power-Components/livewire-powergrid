<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use NumberFormatter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\{Dish};
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    Components\SetUp\Exportable,
    Facades\PowerGrid,
    PowerGridComponent,
    PowerGridFields};

class DishesMakeTable extends PowerGridComponent
{
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

    public function fields(): PowerGridFields
    {
        $fmt = new NumberFormatter('ca_ES', NumberFormatter::CURRENCY);

        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('storage_room')
            ->add('chef_name')
            ->add('serving_at')
            ->add('calories')
            ->add('calories', function ($dish) {
                return $dish->calories . ' kcal';
            })
            ->add('category_id', function ($dish) {
                return $dish->category_id;
            })
            ->add('category_name', function ($dish) {
                return $dish->category->name;
            })
            ->add('price')
            ->add('price_EUR', function ($dish) use ($fmt) {
                return $fmt->formatCurrency($dish->price, 'EUR');
            })
            ->add('price_BRL', function ($dish) {
                return 'R$ ' . number_format($dish->price, 2, ',', '.'); //R$ 1.000,00
            })
            ->add('sales_price')
            ->add('sales_price_BRL', function ($dish) {
                $sales_price = $dish->price + ($dish->price * 0.15);

                return 'R$ ' . number_format($sales_price, 2, ',', '.'); //R$ 1.000,00
            })
            ->add('in_stock')
            ->add('in_stock_label', function ($dish) {
                return ($dish->in_stock ? 'sim' : 'não');
            })
            ->add('produced_at')
            ->add('produced_at_formatted', function ($dish) {
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
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::make('Serving at', 'serving_at')
                ->sortable(),

            Column::make('Chef', 'chef_name')
                ->searchable()
                ->placeholder('Chef placeholder')
                ->sortable(),

            Column::make('Category', 'category_name')
                ->placeholder('Category'),

            Column::make('Preço', 'price_BRL'),

            Column::make('Preço de Venda', 'sales_price_BRL'),

            Column::make('Calorias', 'calories')
                ->sortable(),

            Column::make('Em Estoque', 'in_stock')
                ->toggleable(true, 'sim', 'não'),

            Column::make('Data de produção', 'produced_at_formatted'),

            Column::make('Data', 'produced_at')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function actions($dish): array
    {
        return [
            Button::make('edit-stock', '<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => $dish->id]),

            Button::make('destroy', 'Delete')
                ->class('text-center')
                ->dispatch('deletedEvent', ['dishId' => $dish->id]),
        ];
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
}
