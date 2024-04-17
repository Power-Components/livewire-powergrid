<?php

use Illuminate\Support\Facades\{Cookie};
use Livewire\Features\SupportTesting\Testable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\{PowerGridComponent};

$component = new class () extends DishTableBase {
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
    'tailwind -> id'  => [$component::class, 'tailwind', 'name'],
    'bootstrap -> id' => [$component::class, 'bootstrap', 'name'],
];

it('should be able to set persist_driver for session', function (string $componentString, string $theme, string $field) {
    config()->set('livewire-powergrid.persist_driver', 'session');

    $component = livewire($componentString)
        ->call($theme);

    /** @var PowerGridComponent $component */
    expect($component->filters)
        ->toMatchArray([]);

    /** @var Testable $component */
    $component->call('filterInputText', $field, 'ba', 'test');

    expect(session('pg:default'))->toBe('{"filters":[],"enabledFilters":[{"field":"' . $field . '","label":"test"}]}');
})->group('filters')
    ->with($params);

it('should be able to set persist_driver for cookies', function (string $componentString, string $theme, string $field) {
    config()->set('livewire-powergrid.persist_driver', 'cookies');

    $component = livewire($componentString)
        ->call($theme);

    /** @var PowerGridComponent $component */
    expect($component->filters)
        ->toMatchArray([]);

    /** @var Testable $component */
    $component->call('filterInputText', $field, 'ba', 'test');

    expect(Cookie::queued('pg:default')->getValue())->toBe('{"filters":[],"enabledFilters":[{"field":"' . $field . '","label":"test"}]}');
})
    ->with($params);

it('should not be able to set invalid persist driver', function (string $componentString, string $theme) {
    # change config
    config()->set('livewire-powergrid.persist_driver', 'invalid');

    expect(static function () use ($componentString, $theme) {
        livewire($componentString)
            ->call($theme);
    })->toThrow(Exception::class);
})
    ->with($params);
