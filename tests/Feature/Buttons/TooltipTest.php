<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('tooltip')
                ->slot('tooltip: ' . $row->id)
                ->tooltip('This is a custom title for Dish ' . $row->id . '!'),
        ];
    }
};

dataset('action:tooltip', [
    'tailwind'       => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "tooltip" on edit button', function (string $component, object $params) {
    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml("title=\"This is a custom title for Dish 1!\">tooltip: 1</button>")
        ->assertSeeHtml("title=\"This is a custom title for Dish 2!\">tooltip: 2</button>")
        ->assertSeeHtml("title=\"This is a custom title for Dish 3!\">tooltip: 3</button>")
        ->call('setPage', 2)
        ->assertSeeHtml("title=\"This is a custom title for Dish 7!\">tooltip: 7</button>")
        ->assertDontSeeHtml("title=\"This is a custom title for Dish 1!\">tooltip: 1</button>");
})
    ->with('action:tooltip')
    ->group('action');
