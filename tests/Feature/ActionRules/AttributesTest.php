<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

;

$simple = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->class('text-center'),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 1)
                ->setAttribute('class', 'bg-pg-primary-400'),
        ];
    }
};

$customAttributes = new class () extends DishTableBase {
    public int $dishId;

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->class('text-center'),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == $this->dishId)
                ->setAttribute('class', 'bg-pg-primary-200')
                ->setAttribute('title', 'Title changed by setAttributes when id 2')
                ->setAttribute('wire:click', ['test', ['param1' => $this->dishId, 'dishId' => $this->dishId]]),
        ];
    }
};

dataset('actionRules:simple', [
    'tailwind'       => [$simple::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$simple::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$simple::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$simple::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

dataset('actionRules:attributes', [
    'tailwind'       => [$customAttributes::class, (object) ['theme' => 'tailwind', 'join' => false, 'dishId' => 2]],
    'bootstrap'      => [$customAttributes::class, (object) ['theme' => 'bootstrap', 'join' => false, 'dishId' => 2]],
    'tailwind join'  => [$customAttributes::class, (object) ['theme' => 'tailwind', 'join' => true, 'dishId' => 2]],
    'bootstrap join' => [$customAttributes::class, (object) ['theme' => 'bootstrap', 'join' => true, 'dishId' => 2]],
]);

it('set custom class when dish-id == 1 on button', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtmlInOrder([
            'class="bg-pg-primary-400 text-center"',
        ]);
})->with('actionRules:simple')
    ->group('actionRules');

it('change class to bg-pg-primary-100 when dish-id == 1 -tailwind', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtmlInOrder([
            '<tr',
            'style',
            'class="bg-pg-primary-400 border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700"',
        ])
        ->set('search', 'Barco-Sushi da Sueli')
        ->assertDontSeeHtml(
            'class="bg-pg-primary-400 border-b border-pg-primary-100 dark:border-pg-primary-400 hover:bg-pg-primary-50 dark:bg-pg-primary-700 dark:odd:bg-pg-primary-800 dark:odd:hover:bg-pg-primary-900 dark:hover:bg-pg-primary-700"',
        );
})->with(
    [
        'tailwind'      => [$simple::class, (object) ['theme' => 'tailwind', 'join' => false]],
        'tailwind join' => [$simple::class, (object) ['theme' => 'tailwind', 'join' => true]],
    ]
)->group('actionRules');

it('change class to bg-pg-primary-100 when dish-id == 1 - bootstrap', function (string $component, object $params) {
    livewire($component, ['join' => $params->join, 'dishId' => 1])
        ->call($params->theme)
        ->set('search', 'Pastel de Nata')
        ->assertSeeHtmlInOrder([
            '<tr',
            'class="bg-pg-primary-400"',
        ])
        ->set('search', 'Barco-Sushi da Sueli')
        ->assertDontSeeHtml(
            'class="bg-pg-primary-400"',
        );
})->with(
    [
        'bootstrap'      => [$simple::class, (object) ['theme' => 'bootstrap', 'join' => false, 'dishId' => 1]],
        'bootstrap join' => [$simple::class, (object) ['theme' => 'bootstrap', 'join' => true, 'dishId' => 1]],
    ]
)->group('actionRules');

it('see many custom \'attributes\' when dish-id == 2', function (string $component, object $params) {
    livewire($component, [
        'join'   => $params->join,
        'dishId' => 2,
    ])
        ->call($params->theme)
        ->set('search', 'Peixada da chef Nábia')
        ->assertSeeHtml(
            <<<HTML
<button wire:click="test(JSON.parse(&amp;#039;{\u0022param1\u0022:2,\u0022dishId\u0022:2}&amp;#039;))" class="bg-pg-primary-200 text-center" title="Title changed by setAttributes when id 2">Edit: 2</button>
HTML
        );
})->with('actionRules:attributes')->group('actionRules');
