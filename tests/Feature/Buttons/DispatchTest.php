<?php

use PowerComponents\LivewirePowerGrid\Button;
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
};

dataset('action:dispatch', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "dispatch" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("\$dispatch(&#039;executeDispatch&#039;, JSON.parse(&#039;{\u0022id\u0022:1}&#039;")
        ->assertSeeHtml("\$dispatch(&#039;executeDispatch&#039;, JSON.parse(&#039;{\u0022id\u0022:1}&#039;")
        ->assertDontSeeHtml("\$dispatch(&#039;executeDispatch&#039;, JSON.parse(&#039;{\u0022id\u0022:7}&#039;")
        ->call('setPage', 2)
        ->assertSeeHtml("\$dispatch(&#039;executeDispatch&#039;, JSON.parse(&#039;{\u0022id\u0022:7}&#039;")
        ->assertDontSeeHtml("\$dispatch(&#039;executeDispatch&#039;, JSON.parse(&#039;{\u0022id\u0022:1}&#039;");
})
    ->with('action:dispatch')
    ->group('action');
