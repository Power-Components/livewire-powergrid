<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use NumberFormatter;
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

class DishesTable extends PowerGridComponent
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

    public function fields(): PowerGridFields
    {
        $fmt = new NumberFormatter('ca_ES', NumberFormatter::CURRENCY);

        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('storage_room')
            ->add('chef_name')
            ->add('active')
            ->add('serving_at')
            ->add('calories')
            ->add('calories', function (Dish $dish) {
                return $dish->calories . ' kcal';
            })
            ->add('category_id', function (Dish $dish) {
                return $dish->category_id;
            })
            ->add('category_name', function (Dish $dish) {
                return $dish->category->name;
            })
            ->add('price')
            ->add('price_EUR', function (Dish $dish) use ($fmt) {
                return $fmt->formatCurrency($dish->price, 'EUR');
            })
            ->add('price_BRL', function (Dish $dish) {
                return 'R$ ' . number_format($dish->price, 2, ',', '.'); //R$ 1.000,00
            })
            ->add('sales_price')
            ->add('sales_price_BRL', function (Dish $dish) {
                $sales_price = $dish->price + ($dish->price * 0.15);

                return 'R$ ' . number_format($sales_price, 2, ',', '.'); //R$ 1.000,00
            })
            ->add('in_stock')
            ->add('in_stock_label', function (Dish $dish) {
                return ($dish->in_stock ? 'sim' : 'não');
            })
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
                ->title(__('Stored at'))
                ->field('storage_room')
                ->sortable(),

            Column::add()
                ->title(__('Prato'))
                ->field('name')
                ->searchable()
                ->placeholder('Prato placeholder')
                ->sortable(),

            Column::add()
                ->title('Serving at')
                ->field('serving_at')
                ->sortable(),

            Column::add()
                ->title(__('Chef'))
                ->field('chef_name')
                ->searchable()
                ->placeholder('Chef placeholder')
                ->sortable(),

            Column::add()
                ->title(__('Category'))
                ->field('category_name')
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
                ->field('in_stock'),

            Column::add()
                ->title(__('Data de produção'))
                ->field('produced_at_formatted'),

            Column::add()
                ->title(__('Data'))
                ->field('produced_at')
                ->sortable(),

            Column::action('Action'),
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
