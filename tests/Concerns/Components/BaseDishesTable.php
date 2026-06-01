<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Carbon\Carbon;
use NumberFormatter;
use PowerComponents\LivewirePowerGrid\{Column,
    Components\SetUp\Exportable,
    Facades\PowerGrid,
    PowerGridComponent,
    PowerGridFields};

abstract class BaseDishesTable extends PowerGridComponent
{
    public array $eventId = [];

    public array $testFilters = [];

    public function getListeners(): array
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

    public function filters(): array
    {
        return $this->testFilters;
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
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
                return $dish->calories.' kcal';
            })
            ->add('category_id', function ($dish) {
                return $dish->category_id;
            })
            ->add('category_name', fn ($dish) => $this->getCategoryName($dish))
            ->add('price')
            ->add('price_EUR', function ($dish) use ($fmt) {
                return $fmt->formatCurrency($dish->price, 'EUR');
            })
            ->add('price_BRL', function ($dish) {
                return 'R$ '.number_format($dish->price, 2, ',', '.');
            })
            ->add('sales_price')
            ->add('sales_price_BRL', function ($dish) {
                $sales_price = $dish->price + ($dish->price * 0.15);

                return 'R$ '.number_format($sales_price, 2, ',', '.');
            })
            ->add('in_stock')
            ->add('in_stock_label', function ($dish) {
                return $dish->in_stock ? 'yes' : 'no';
            })
            ->add('produced_at')
            ->add('produced_at_formatted', function ($dish) {
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
                ->placeholder('Dish placeholder')
                ->sortable(),

            Column::add()
                ->title('Serving at')
                ->field('serving_at')
                ->sortable(),

            Column::add()
                ->title('Chef')
                ->field('chef_name')
                ->searchable()
                ->placeholder('Chef placeholder')
                ->sortable(),

            Column::add()
                ->title('Category')
                ->field('category_name')
                ->placeholder('Category placeholder'),

            Column::add()
                ->title('Price')
                ->field('price_BRL'),

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
                ->title('Production Date')
                ->field('produced_at_formatted'),

            Column::add()
                ->title('Date')
                ->field('produced_at')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    protected function getCategoryName($dish): string
    {
        return $dish->category->name ?? $dish->category_name ?? '';
    }

    abstract public function datasource();
}
