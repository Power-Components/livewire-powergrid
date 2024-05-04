<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesBeforeSearchTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches data using beforeSearch', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('search', 'Pastel')
        ->assertSee('Peixada')
        ->set('search', 'Francesinha')
        ->assertSee('Peixada')
        ->set('search', '')
        ->assertSee('Peixada');
})->with([
    'tailwind'  => [DishesBeforeSearchTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [DishesBeforeSearchTable::class, (object) ['theme' => 'bootstrap']],
]);

it('can use beforeSearch in boolean field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        # without_stock => in_stock = 0
        ->set('search', 'without_stock')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Carne Louca')
        ->assertSee('Barco-Sushi Simples')
        # with_stock => in_stock = 1
        ->set('search', 'with_stock')
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Barco-Sushi Simples')
        # without_stock => in_stock = 0
        ->set('search', 'without_stock')
        ->assertDontSee('Pastel de Nata')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Pastel de Nata');
})->with([
    'tailwind'  => [DishesBeforeSearchTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [DishesBeforeSearchTable::class, (object) ['theme' => 'bootstrap']],
]);
