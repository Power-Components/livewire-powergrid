<?php

use function Pest\Livewire\livewire;
use PowerComponents\LivewirePowerGrid\Tests\RulesCaptionTable;

it('add rule \'caption\' when dish out of stock', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtml('<div id="edit">Edit</div>')
        ->assertDontSeeHtml('Cation Edit for id 4')
        ->set('search', 'Bife à Rolê')
        ->assertDontSeeHtml('<div id="edit">Edit</div>')
        ->assertSeeHtml('Cation Edit for id 4');
})->with('caption')->group('actionRules');

dataset('caption', [
    'tailwind'       => [RulesCaptionTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesCaptionTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesCaptionTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesCaptionTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
