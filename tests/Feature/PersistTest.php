<?php

use Illuminate\Support\Facades\Cookie;
use Livewire\Features\SupportTesting\Testable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, DaisyUI, Tailwind};

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class() extends DishTableBase
{
    public function setUp(): array
    {
        $this->persist(['filters', 'enabledFilters']);

        return parent::setUp();
    }

    public function filters(): array
    {
        return array_merge(parent::filters(), [
            Filter::inputText('name'),
        ]);
    }
};

$params = [
    'tailwind -> id' => [$component::class, Tailwind::class, 'name'],
    'bootstrap -> id' => [$component::class, Bootstrap5::class, 'name'],
    'daisyui -> id' => [$component::class, DaisyUI::class, 'name'],
];

$multiSortComponent = new class() extends DishTableBase
{
    public bool $multiSort = true;

    public function setUp(): array
    {
        $this->persist(['sorting']);

        return parent::setUp();
    }
};

$multiSortParams = [
    'tailwind' => [$multiSortComponent::class, Tailwind::class],
    'bootstrap' => [$multiSortComponent::class, Bootstrap5::class],
    'daisyui' => [$multiSortComponent::class, DaisyUI::class],
];

it('should be able to set persist_driver for session', function (string $componentString, string $theme, string $field) {
    config()->set('livewire-powergrid.persist_driver', 'session');

    $component = livewire($componentString)
        ->call('setTestThemeClass', $theme);

    /** @var PowerGridComponent $component */
    expect($component->filters)
        ->toMatchArray([]);

    /** @var Testable $component */
    $component->call('filterInputText', $field, 'ba', 'test');

    expect(session('pg:testing-dish-table'))->toBe('{"filters":[],"enabledFilters":[{"field":"'.$field.'","label":"test"}]}');
})->group('filters')
    ->with($params);

it('should be able to set persist_driver for cookies', function (string $componentString, string $theme, string $field) {
    config()->set('livewire-powergrid.persist_driver', 'cookies');

    $component = livewire($componentString)
        ->call('setTestThemeClass', $theme);

    /** @var PowerGridComponent $component */
    expect($component->filters)
        ->toMatchArray([]);

    /** @var Testable $component */
    $component->call('filterInputText', $field, 'ba', 'test');

    expect(Cookie::queued('pg:testing-dish-table')->getValue())->toBe('{"filters":[],"enabledFilters":[{"field":"'.$field.'","label":"test"}]}');
})
    ->with($params);

it('should persist sorting state when multiSort is enabled', function (string $componentString, string $theme) {
    config()->set('livewire-powergrid.persist_driver', 'session');

    livewire($componentString)
        ->call('setTestThemeClass', $theme)
        ->call('sortBy', 'name')
        ->call('sortBy', 'id');

    expect(session('pg:testing-dish-table'))
        ->toBe('{"sortField":"id","sortDirection":"asc","sortArray":{"name":"asc","id":"asc"},"multiSort":true}');
})->group('sorting')
    ->with($multiSortParams);

it('should restore the persisted sortArray when multiSort is enabled', function (string $componentString, string $theme) {
    config()->set('livewire-powergrid.persist_driver', 'session');

    livewire($componentString)
        ->call('setTestThemeClass', $theme)
        ->call('sortBy', 'name');

    // A fresh mount must read the sortArray back from the persist storage.
    $component = livewire($componentString)
        ->call('setTestThemeClass', $theme);

    /** @var PowerGridComponent $component */
    expect($component->sortArray)
        ->toMatchArray(['name' => 'asc'])
        ->and($component->multiSort)
        ->toBeTrue();
})->group('sorting')
    ->with($multiSortParams);

it('should not be able to set invalid persist driver', function (string $componentString, string $theme) {
    // change config
    config()->set('livewire-powergrid.persist_driver', 'invalid');

    expect(static function () use ($componentString, $theme) {
        livewire($componentString)
            ->call('setTestThemeClass', $theme);
    })->toThrow(Exception::class);
})
    ->with($params);
