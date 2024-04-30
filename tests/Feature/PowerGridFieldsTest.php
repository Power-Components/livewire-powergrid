<?php
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Order;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};

$component = new class () extends PowerGridComponent {
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
            ->add('price_formatted', fn (Order $model) => $model->price * 100);
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Price', 'price_formatted', 'price'),
            Column::make('Tax', 'tax'),
        ];
    }
};

it('can add fields', function (string $name, string|float $price, string|float $tax) use ($component) {
    $component = livewire($component::class);

    $component->assertSee($name)
              ->assertSee($price)
              ->assertSee($tax);
})->with([
    ['Order 1', 1000, 127.30],
    ['Order 2', 2000, 259.50],
    ['Order 3', '', ''],
]);
