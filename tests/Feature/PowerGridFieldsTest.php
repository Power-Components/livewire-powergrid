<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\OrderTable;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Order;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('can fields with casting and custom fields', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtmlInOrder(['Order 1', 'Order 2', 'Order 3'])
        ->assertSeeHtmlInOrder(['active', 'active', 'inactive'])
        ->assertSeeHtmlInOrder(['1000', '2000', '0'])
        ->assertSeeHtmlInOrder(['127.3', '259.5', '']);
})->with('order_table');

dataset('order_table', [
    'tailwind'  => [OrderTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [OrderTable::class, (object) ['theme' => 'bootstrap']],
]);
