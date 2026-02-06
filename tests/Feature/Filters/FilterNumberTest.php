<?php

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

require __DIR__.'/../../Concerns/Components/ComponentsForFilterTest.php';

it('properly filters by filter Number', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    $filters = array_merge($component->filters, filterNumber('price', min: '1\'500.20', max: '3\'000.00'));

    $component->set('filters', $filters)
        ->assertSeeHtml('placeholder="min_xyz_placeholder"')
        ->assertSeeHtml('placeholder="max_xyz_placeholder"')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ');

    expect($component->filters)->toBe($filters);
})->group('filters')
    ->with('filterComponent');

it('properly filters by filter Number with wrong separators', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    // Use wrong separators
    $filters = array_merge($component->filters, filterNumber('price', min: '1@500#20', max: '3@000#00'));

    $component->set('filters', $filters)
        ->assertSee('No records found');
})
    ->skipOnPostgreSQL('PG will throw "invalid input syntax for type double precision"')
    ->group('filters')
    ->with('filterComponent');

$defaultNumberRange = new class() extends \PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable
{
    public function filters(): array
    {
        return [
            \PowerComponents\LivewirePowerGrid\Facades\Filter::number('price')
                ->thousands("'")
                ->decimal(',')
                ->default(['start' => '1\'500.20', 'end' => '3\'000.00']),
        ];
    }
};

$defaultNumberStart = new class() extends \PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable
{
    public function filters(): array
    {
        return [
            \PowerComponents\LivewirePowerGrid\Facades\Filter::number('price')
                ->thousands("'")
                ->decimal(',')
                ->default(100),
        ];
    }
};

it('applies default value range to number filter on initial load', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'number' => [
            'price' => [
                'start' => '1\'500.20',
                'end' => '3\'000.00',
            ],
        ],
    ]);

    // Should show items within price range
    $component->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Polpetone Filé Mignon');
})->group('filters')
    ->with([
        'tailwind' => [$defaultNumberRange::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultNumberRange::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultNumberRange::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('applies default single value to number filter as start', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'number' => [
            'price' => [
                'start' => 100,
            ],
        ],
    ]);
})->group('filters')
    ->with([
        'tailwind' => [$defaultNumberStart::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultNumberStart::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultNumberStart::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear default number filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'number' => [
            'price' => [
                'start' => '1\'500.20',
                'end' => '3\'000.00',
            ],
        ],
    ]);

    $component->call('clearFilter', 'price');

    expect($component->filters)->toMatchArray([]);

    // Should now show all items including previously filtered out ones
    $component->assertSee('Barco-Sushi da Sueli');
})->group('filters')
    ->with([
        'tailwind' => [$defaultNumberRange::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultNumberRange::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultNumberRange::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);
