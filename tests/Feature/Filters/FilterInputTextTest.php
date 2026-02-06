<?php

use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

require __DIR__.'/../../Concerns/Components/ComponentsForFilterTest.php';

it('properly filters by inputText', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    /** @var PowerGridComponent $component */
    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterInputText('ba', 'contains', $params->field));

    if (str_contains($params->field, '.')) {
        $data = Str::of($params->field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $table => [
                        $field => 'ba',
                    ],
                ],
                'input_text_options' => [
                    $table => [
                        $field => 'contains',
                    ],
                ],
            ]);
    } else {
        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $params->field => 'ba',
                ],
                'input_text_options' => [
                    $params->field => 'contains',
                ],
            ]);
    }

    $component->assertSee('Barco-Sushi da Sueli')
        ->assertSeeHtml('dish_name_xyz_placeholder');

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

it('properly filters by inputText using action', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    $component->set('filters.input_text.'.$params->field, 'ba')
        ->call('filterInputText', $params->field, 'ba', 'Dish Name');

    if (str_contains($params->field, '.')) {
        $data = Str::of($params->field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $table => [
                        $field => 'ba',
                    ],
                ],
            ]);
    } else {
        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $params->field => 'ba',
                ],
            ]);
    }
})->group('filters')
    ->with('filterComponent');

it('properly filters by inputTextOptions using action', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    $component->call('filterInputTextOptions', $params->field, 'contains_not', 'Dish Name');

    if (str_contains($params->field, '.')) {
        $data = Str::of($params->field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        expect($component->filters)
            ->toMatchArray([
                'input_text_options' => [
                    $table => [
                        $field => 'contains_not',
                    ],
                ],
            ]);
    } else {
        expect($component->filters)
            ->toMatchArray([
                'input_text_options' => [
                    $params->field => 'contains_not',
                ],
            ]);
    }
})->group('filters')
    ->with('filterComponent');

it('properly filters by inputTextOptions is_empty', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    $component->call('filterInputTextOptions', $params->field, 'is_empty', 'Dish Name');

    if (str_contains($params->field, '.')) {
        $data = Str::of($params->field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        expect($component->filters)
            ->toMatchArray([
                'input_text_options' => [
                    $table => [
                        $field => 'is_empty',
                    ],
                ],
                'input_text' => [
                    $table => [
                        $field => null,
                    ],
                ],
            ]);
    } else {
        expect($component->filters)
            ->toMatchArray([
                'input_text_options' => [
                    $params->field => 'is_empty',
                ],
                'input_text' => [
                    $params->field => null,
                ],
            ]);
    }
})->group('filters')
    ->with('filterComponent');

$defaultInputText = new class() extends \PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable
{
    public function filters(): array
    {
        return [
            \PowerComponents\LivewirePowerGrid\Facades\Filter::inputText('name')
                ->operators()
                ->default('Pastel'),
        ];
    }
};

$defaultInputTextWithOperator = new class() extends \PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable
{
    public function filters(): array
    {
        return [
            \PowerComponents\LivewirePowerGrid\Facades\Filter::inputText('name')
                ->operators()
                ->default(['value' => 'Pastel', 'operator' => 'contains']),
        ];
    }
};

it('applies default value to input text filter on initial load', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'input_text' => [
            'name' => 'Pastel',
        ],
    ]);

    // Should show items matching "Pastel"
    $component->assertSee('Pastel de Nata')
        ->assertDontSee('Barco-Sushi da Sueli');
})->group('filters')
    ->with([
        'tailwind' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('applies default value with operator to input text filter on initial load', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'input_text' => [
            'name' => 'Pastel',
        ],
        'input_text_options' => [
            'name' => 'contains',
        ],
    ]);

    // Should show items containing "Pastel"
    $component->assertSee('Pastel de Nata')
        ->assertDontSee('Barco-Sushi da Sueli');
})->group('filters')
    ->with([
        'tailwind' => [$defaultInputTextWithOperator::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultInputTextWithOperator::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultInputTextWithOperator::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear default input text filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'input_text' => [
            'name' => 'Pastel',
        ],
    ]);

    $component->call('clearFilter', 'name');

    expect($component->filters)->toMatchArray([]);

    // Should now show all items
    $component->assertSee('Barco-Sushi da Sueli');
})->group('filters')
    ->with([
        'tailwind' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);
