<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('confirm-prompt')
                ->slot('confirm-prompt: ' . $row->id)
                ->confirmPrompt("$row->id Are you sure? Enter CONFIRM to confirm", "CONFIRM"),
        ];
    }
};

dataset('action:confirm-prompt', [
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
        ->assertSeeHtml('wire:confirm.prompt="1 Are you sure? Enter CONFIRM to confirm |CONFIRM"')
        ->assertDontSeeHtml('wire:confirm.prompt="2 Are you sure? Enter CONFIRM to confirm |CONFIRM"')
        ->set('search', 'Peixada da chef Nábia')
        ->assertSeeHtml('wire:confirm.prompt="2 Are you sure? Enter CONFIRM to confirm |CONFIRM"')
        ->assertDontSeeHtml('wire:confirm.prompt="1 Are you sure? Enter CONFIRM to confirm |CONFIRM"');
})
    ->with('action:confirm-prompt')
    ->group('action');
