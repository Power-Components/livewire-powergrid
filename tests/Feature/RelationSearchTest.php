<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\RelationSearchTable;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches data using relation search', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('search', 'Sobremesas')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('борщ')
        ->set('search', 'Pastel de Nata')
        ->assertSee('Pastel de Nata')
        ->set('search', 'Sopas')
        ->assertSee('борщ')
        ->set('search', 'борщ')
        ->assertSee('борщ')
        ->assertDontSee('Pastel de Nata');
})->with([
    'tailwind' => [RelationSearchTable::class, (object) ['theme' => Tailwind::class]],
]);
