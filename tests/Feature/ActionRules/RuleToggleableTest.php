<?php

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

require(__DIR__ . '/../../Concerns/Components/ComponentsForTestRule.php');

it('properly hides toggleable with Row->hideToggleable() for dish #5', function (string $baseRuleComponent, object $params) {
    livewire($baseRuleComponent, [
        'join' => $params->join,
    ])->call($params->theme)
    ->set('setUp.footer.perPage', 5)
    ->assertDontSeeHtml("pgToggleable(JSON.parse('{\u0022id\u0022:5,\u0022isHidden\u0022:false,\u0022tableName\u0022:\u0022default\u0022,\u0022field\u0022:\u0022in_stock\u0022,")
    ->assertSeeHtmlInOrder(
        [
            "pgToggleable(JSON.parse('{\u0022id\u0022:3,\u0022isHidden\u0022:false,\u0022tableName\u0022:\u0022default\u0022,\u0022field\u0022:\u0022in_stock\u0022,",

            "pgToggleable(JSON.parse('{\u0022id\u0022:4,\u0022isHidden\u0022:false,\u0022tableName\u0022:\u0022default\u0022,\u0022field\u0022:\u0022in_stock\u0022,",
        ]
    );
})
    ->with([
        'tailwind using when'       => [$baseRuleComponent::class, (object) ['theme' => 'tailwind', 'join' => false]],
        'bootstrap using when'      => [$baseRuleComponent::class, (object) ['theme' => 'bootstrap', 'join' => false]],
        'tailwind join using when'  => [$baseRuleComponent::class, (object) ['theme' => 'tailwind', 'join' => true]],
        'bootstrap join using when' => [$baseRuleComponent::class, (object) ['theme' => 'bootstrap', 'join' => true],
        ],
    ])
    ->group('actionRules');

it('properly shows toggleable with Rule::toggleable for dish #1', function (string $baseRuleComponent, object $params) {
    livewire($baseRuleComponent, [
        'join' => $params->join,
    ])->call($params->theme)
    ->set('setUp.footer.perPage', 5)
    ->assertSeeHtml("pgToggleable(JSON.parse('{\u0022id\u0022:1,\u0022isHidden\u0022:false,\u0022tableName\u0022:\u0022default\u0022,\u0022field\u0022:\u0022active\u0022,")
    ->assertSeeHtml("pgToggleable(JSON.parse('{\u0022id\u0022:2,\u0022isHidden\u0022:true,\u0022tableName\u0022:\u0022default\u0022,\u0022field\u0022:\u0022active\u0022,")
    ->assertSeeHtml("pgToggleable(JSON.parse('{\u0022id\u0022:3,\u0022isHidden\u0022:true,\u0022tableName\u0022:\u0022default\u0022,\u0022field\u0022:\u0022active\u0022,");
})
    ->with([
        'tailwind using when'       => [$baseRuleComponent::class, (object) ['theme' => 'tailwind', 'join' => false]],
        'bootstrap using when'      => [$baseRuleComponent::class, (object) ['theme' => 'bootstrap', 'join' => false]],
        'tailwind join using when'  => [$baseRuleComponent::class, (object) ['theme' => 'tailwind', 'join' => true]],
        'bootstrap join using when' => [$baseRuleComponent::class, (object) ['theme' => 'bootstrap', 'join' => true],
        ],
    ])
    ->group('actionRules');
