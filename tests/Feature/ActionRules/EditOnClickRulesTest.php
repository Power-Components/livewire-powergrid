<?php

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

require(__DIR__ . '/../../Concerns/Components/ComponentsForTestRule.php');

it('properly enabled EditOnClick with Rule::toggleable for dish #1 in a column that has it disabled', function (string $baseRuleComponent, object $params) {
    livewire($baseRuleComponent, [
        'join' => $params->join,
    ])->call($params->theme)
    ->set('setUp.footer.perPage', 5)
    ->assertSeeHtml("pgEditable(JSON.parse('{\u0022theme\u0022:\u0022{$params->themeName}\u0022,\u0022tableName\u0022:\u0022default\u0022,\u0022id\u0022:1,\u0022dataField\u0022:\u0022serving_at")
    ->assertDontSeeHtml("pgEditable(JSON.parse('{\u0022theme\u0022:\u0022{$params->themeName}\u0022,\u0022tableName\u0022:\u0022default\u0022,\u0022id\u0022:2,\u0022dataField\u0022:\u0022serving_at")
    ->assertDontSeeHtml("pgEditable(JSON.parse('{\u0022theme\u0022:\u0022{$params->themeName}\u0022,\u0022tableName\u0022:\u0022default\u0022,\u0022id\u0022:3,\u0022dataField\u0022:\u0022serving_at");
})
    ->with([
        'tailwind using when'       => [$baseRuleComponent::class, (object) ['theme' => 'tailwind', 'join' => false, 'themeName' => 'tailwind']],
        'bootstrap using when'      => [$baseRuleComponent::class, (object) ['theme' => 'bootstrap', 'join' => false, 'themeName' => 'bootstrap5']],
        'tailwind join using when'  => [$baseRuleComponent::class, (object) ['theme' => 'tailwind', 'join' => true, 'themeName' => 'tailwind']],
        'bootstrap join using when' => [$baseRuleComponent::class, (object) ['theme' => 'bootstrap', 'join' => true, 'themeName' => 'bootstrap5']],
    ])
    ->group('actionRules');
