<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Order;
use PowerComponents\LivewirePowerGrid\{
    Column,
    PowerGrid,
    PowerGridComponent,
    PowerGridFields,
};

class OrderTable extends PowerGridComponent
{
    public function datasource(): Builder
    {
        return Order::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('tax')
            ->add('price')
            ->add('link', fn (Order $order): string|null => $order->link)
            ->add('is_active_label', fn (Order $order): string => $order->price ? 'active' : 'inactive')
            ->add('price_formatted', fn (Order $order): float => $order->price * 100);
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Link', 'link'),
            Column::make('Is Active', 'is_active_label'),
            Column::make('Price', 'price_formatted', 'price'),
            Column::make('Tax', 'tax'),
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
