<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$bladeComponent = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('bladeComponent')
                ->bladeComponent('livewire-powergrid::icons.arrow', ['dish-id' => $row->id]),
        ];
    }
};

dataset('bladeComponent', [
    'tailwind'       => [$bladeComponent::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$bladeComponent::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$bladeComponent::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$bladeComponent::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "bladeComponent" on bladeComponent button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml('<svg', 'dish-id="12"')
        ->assertSeeHtml('<svg', 'dish-id="2"')
        ->assertSeeHtml('<path', 'stroke-linecap="round"', 'stroke-linejoin="round"', 'd="M9 5l7 7-7 7"', '/>')
        ->assertDontSeeHtml('<svg dish-id="7"')
        ->call('setPage', 2)
        ->assertDontSeeHtml('<svg dish-id="6"')
        ->assertDontSeeHtml('<svg dish-id="1"');
})->with('bladeComponent')->group('action');
