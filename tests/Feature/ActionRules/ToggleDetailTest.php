<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\RulesToggleDetailTable;

it('add rule \'toggleDetail\' when dishId == 3', function () {
    livewire(RulesToggleDetailTable::class)
        ->set('testActions', [
            Button::make('toggleDetail', 'Toggle Detail')
                ->class('text-center')
                ->toggleDetail(),
        ])
        ->set('testActionRules', [
            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 3)
                ->detailView('livewire-powergrid::tests.detail-rules', ['newParameter' => 1]),
        ])
        ->assertSee('Pastel de Nata')
        ->assertDontSeeHtml([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ]) // show detail row #1
        ->call('toggleDetail', 1)
        ->assertSeeHtmlInOrder([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
        ])
        ->call('toggleDetail', 3)
        ->assertSeeHtmlInOrder([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
            '<div>Id 3</div>',
            '<div>Options {"name":"Luan","newParameter":1}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => true,
            4 => false,
            5 => false,
        ])
        ->call('toggleDetail', 4)
        ->assertSeeHtmlInOrder([
            '<div>Id 1</div>',
            '<div>Options {"name":"Luan"}</div>',
            '<div>Id 3</div>',
            '<div>Options {"name":"Luan"}</div>',
        ])
        ->assertSet('setUp.detail.state', [
            1 => true,
            2 => false,
            3 => true,
            4 => true,
            5 => false,
        ]);
})->group('actionRules');
