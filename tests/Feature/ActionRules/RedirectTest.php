<?php

use function Pest\Livewire\livewire;
use PowerComponents\LivewirePowerGrid\Tests\RulesRedirectTable;

it('add rule \'redirect\' when out of stock and dishId !== 8', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('setUp.footer.perPage', 10)
        ->assertSeeHtml(['href="https://www.dish.test/sorry-out-of-stock?dish=6', 'target="_blank"'])
        ->assertSeeHtml(['href="https://www.dish.test/sorry-out-of-stock?dish=7', 'target="_blank"'])
        ->assertSeeHtml('wire:click="$emit(&quot;openModal&quot;, &quot;modal-edit&quot;, {&quot;dishId&quot;:5})"')
        ->assertSeeHtml(['href="https://www.dish.test/sorry-out-of-stock?dish=8', 'target="_blank"'])
        ->assertSeeHtml(['href="https://www.dish.test/sorry-out-of-stock?dish=10', 'target="_blank"']);
})->with('redirect')->group('actionRules');

dataset('redirect', [
    'tailwind'       => [RulesRedirectTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesRedirectTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesRedirectTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesRedirectTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
