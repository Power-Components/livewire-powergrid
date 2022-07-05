<?php

use function Pest\Livewire\livewire;
use PowerComponents\LivewirePowerGrid\Tests\RulesHideTable;

it('add rule \'hide\' when dishId == 2', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->assertSeeHtml('wire:click="$emit(&quot;openModal&quot;, &quot;modal-edit&quot;, {&quot;dishId&quot;:1})"')
        ->assertDontSeeHtml('wire:click="$emit(&quot;openModal&quot;, &quot;modal-edit&quot;, {&quot;dishId&quot;:2})"');
})->with('hide')->group('actionRules');

dataset('hide', [
    'tailwind'       => [RulesHideTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesHideTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesHideTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesHideTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
