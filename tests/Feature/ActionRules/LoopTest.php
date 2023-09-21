<?php

use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actionRules($row): array
    {
        return [
            Rule::rows()
                ->loop(function ($loop) {
                    return $loop->index % 2;
                })
                ->setAttribute('class', '!bg-gunmetal-100'),
        ];
    }
};

dataset('actionRules:loop', [
    'tailwind'      => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'tailwind join' => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
]);

it('set custom class when loop index % 2 on button', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->assertSeeHtmlInOrder([
            'class="border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
            'class="!bg-gunmetal-100 border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
            'class="border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
            'class="!bg-gunmetal-100 border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
        ])
        ->call('setPage', 2)
        ->assertSeeHtmlInOrder([
            'class="border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
            'class="!bg-gunmetal-100 border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
            'class="border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
            'class="!bg-gunmetal-100 border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
        ]);
})->with('actionRules:loop')
    ->group('actionRules');
