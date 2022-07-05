<?php

use function Pest\Livewire\livewire;
use PowerComponents\LivewirePowerGrid\Tests\RulesBladeComponentTable;

it('add rule \'bladeComponent\' when dish-id == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 10)
        ->assertSeeHtml('<svg dish-id="5"')
        ->assertSeeHtml('<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />')
        ->assertDontSeeHtml('<svg dish-id="1"')
        ->set('search', 'Polpetone Filé Mignon')
        ->assertDontSeeHtml('<svg dish-id="9"')
        ->assertDontSeeHtml('<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />');
})->with('bladeComponent')->group('actionRules');

dataset('bladeComponent', [
    'tailwind'       => [RulesBladeComponentTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesBladeComponentTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesBladeComponentTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesBladeComponentTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
