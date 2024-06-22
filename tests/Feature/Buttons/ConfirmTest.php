<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('confirm')
                ->slot('confirm: ' . $row->id)
                ->confirm('Are you sure? ' . $row->id),
        ];
    }
};

dataset('action:confirm', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "confirm" on button click', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml('wire:confirm="Are you sure? 1"')
        ->assertDontSeeHtml('wire:confirm="Are you sure? 2"')
        ->set('search', 'Peixada da chef Nábia')
        ->assertSeeHtml('wire:confirm="Are you sure? 2"')
        ->assertDontSeeHtml('wire:confirm="Are you sure? 1"');
})
    ->with('action:confirm')
    ->group('action');
