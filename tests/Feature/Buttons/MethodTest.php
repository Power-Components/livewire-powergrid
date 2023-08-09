<?php

use Illuminate\Support\Facades\Route;

use PowerComponents\LivewirePowerGrid\Button;

use PowerComponents\LivewirePowerGrid\Tests\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$route = new class () extends DishTableBase {
    public function actions($row): array
    {
        return [
            Button::make('route')
                ->slot('route: ' . $row->id)
                ->class('text-center')
                ->route('testing.route', ['dishId' => $row->id])
                ->method('post'),
        ];
    }
};

dataset('action:routeMethod', [
    'tailwind'       => [$route::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'      => [$route::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'  => [$route::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join' => [$route::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

it('properly displays "routeMethod" on edit button', function (string $component, object $params) {
    Route::get('testing/{dishId}', function ($params) {
        return 'Testing route';
    })->name('testing.route');

    livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('setUp.footer.perPage', 6)
        ->assertSeeHtml(<<<HTML
<form target="_self" action="http://localhost/testing/1" method="post">
    @method('post')
    @csrf
    <button type="submit" class="text-center" id="">route: 1</button>
</form>
HTML)
        ->call('setPage', 2)
        ->assertSeeHtml(<<<HTML
<form target="_self" action="http://localhost/testing/7" method="post">
    @method('post')
    @csrf
    <button type="submit" class="text-center" id="">route: 7</button>
</form>
HTML)
        ->assertDontSeeHtml(<<<HTML
<form target="_self" action="http://localhost/testing/1" method="post">
    @method('post')
    @csrf
    <button type="submit" class="text-center" id="">route: 1</button>
</form>
HTML);
})
    ->with('action:routeMethod')
    ->group('action');
