<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\{livewire};

use PowerComponents\LivewirePowerGrid\Tests\{DishTableBase, RulesEmitTable};

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
                ->when(fn (Dish $dish) => $dish->id == 5)
                ->dispatch('toggleEvent', ['dishId' => 'id']),
        ];
    }
};

it('add rule \'dispatch\' when dishId == 5', function (string $component, object $params) {
    livewire($component, ['join' => $params->join])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 10)
        ->set('search', 'Francesinha vegana')
        ->assertSee('$emit("toggleEvent", {"dishId":5})')
      //  ->assertPayloadNotSet('eventId', ['dishId' => 5])
        ->call('deletedEvent', ['dishId' => 5]);
    //  ->assertPayloadSet('eventId', ['dishId' => 5]);
})->with('emit_themes_with_join')->group('actionRules');

dataset('emit_themes_with_join', [
    'tailwind'       => [RulesEmitTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [RulesEmitTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [RulesEmitTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [RulesEmitTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
