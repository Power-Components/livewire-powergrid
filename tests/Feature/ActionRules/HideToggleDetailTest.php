<?php

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

require(__DIR__ . '/../../Concerns/Components/ComponentsForTestRule.php');

it('properly hides the toggle detail button', function (string $baseRuleComponent, object $params) {
    livewire($baseRuleComponent, [
        'join' => $params->join,
    ])->call($params->theme)
    ->set('setUp.footer.perPage', 5)
    ->assertDontSeeHtml('$wire.toggleDetail(\'1\')')
    ->assertSeeHtml('$wire.toggleDetail(\'2\')')
    ->assertSeeHtml('$wire.toggleDetail(\'3\')');
})
    ->with([
        'tailwind using when'       => [$baseRuleComponent::class, (object) ['theme' => 'tailwind', 'join' => false]],
        'bootstrap using when'      => [$baseRuleComponent::class, (object) ['theme' => 'bootstrap', 'join' => false]],
        'tailwind join using when'  => [$baseRuleComponent::class, (object) ['theme' => 'tailwind', 'join' => true]],
        'bootstrap join using when' => [$baseRuleComponent::class, (object) ['theme' => 'bootstrap', 'join' => true],
        ],
    ])
    ->group('actionRules');
