<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::add('id')
                ->id('open-modal')
                ->slot('id')
                ->class('text-center'),
        ];
    }
};

dataset('id', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "id" on button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml('id="open-modal-1"')
        ->assertDontSeeHtml('id="open-modal-2"')
        ->set('search', 'Peixada da chef Nábia')
        ->assertSeeHtml('id="open-modal-2"')
        ->assertDontSeeHtml('id="open-modal-1"');
})->with('id')->group('action');
