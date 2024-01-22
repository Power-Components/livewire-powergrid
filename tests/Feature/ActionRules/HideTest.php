<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('dispatch')
                ->slot('dispatch: ' . $row->id)
                ->dispatch('executeDispatch', ['id' => $row->id]),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('dispatch')
                ->when(fn ($dish) => $dish->id == 3)
                ->hide(),
        ];
    }
};

$hideWithRender = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('render')
                ->render(function ($row) {
                    return 'render - ' . $row->id;
                }),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('render')
                ->when(fn ($dish) => $dish->id == 3)
                ->hide(),
        ];
    }
};

$hidePreventShowHtml = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('test')
                ->id()
                ->dispatch('edit', ['dishId' => $row->id]),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('edit')
                ->when(fn ($row) => $row->id === 1)
                ->hide(),
        ];
    }
};

it('properly displays "hide" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 3)
        ->assertSeeHtml("\$dispatch(&#039;executeDispatch&#039;, JSON.parse(&#039;{\u0022id\u0022:1}&#039;")
        ->assertSeeHtml("\$dispatch(&#039;executeDispatch&#039;, JSON.parse(&#039;{\u0022id\u0022:2}&#039;")
        ->assertDontSeeHtml("\$dispatch(&#039;executeDispatch&#039;, JSON.parse(&#039;{\u0022id\u0022:3}&#039;");
})
    ->with([
        'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
        'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
        'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
        'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
    ])
    ->group('action');

it('does not show the html of actions when hide is activated', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 3)
        ->assertDontSeeHtml("action-1-render-action.0.edit")
        ->assertSeeHtml("action-2-render-action.0.edit")
        ->assertSeeHtml("action-3-render-action.0.edit");
})
    ->with([
        'tailwind'       => [$hidePreventShowHtml::class, (object) ['theme' => 'tailwind', 'join' => false]],
        'bootstrap'      => [$hidePreventShowHtml::class, (object) ['theme' => 'bootstrap', 'join' => false]],
        'tailwind join'  => [$hidePreventShowHtml::class, (object) ['theme' => 'tailwind', 'join' => true]],
        'bootstrap join' => [$hidePreventShowHtml::class, (object) ['theme' => 'bootstrap', 'join' => true]],
    ])
    ->group('action');
