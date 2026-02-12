<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\{
    Concerns\Components\DishesTable
};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Category;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

// ── Boolean default ───────────────────────────────────────────────────

$defaultBooleanTrue = new class() extends DishesTable
{
    public string $tableName = 'default-boolean-true-table';

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->label('yes', 'no')
                ->default('true'),
        ];
    }
};

it('applies default boolean filter value "true" on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
    ]);

    $component->assertSee('Pastel de Nata')
        ->assertDontSee('Barco-Sushi da Sueli');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

$defaultBooleanFalse = new class() extends DishesTable
{
    public string $tableName = 'default-boolean-false-table';

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->label('yes', 'no')
                ->default('false'),
        ];
    }
};

it('applies default boolean filter value "false" on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'false',
        ],
    ]);

    $component->assertSee('Barco-Sushi da Sueli')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultBooleanFalse::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanFalse::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanFalse::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear a default boolean filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
    ]);

    $component->call('clearFilter', 'in_stock');

    expect($component->filters)->toMatchArray([]);

    $component->assertSee('Pastel de Nata')
        ->assertSee('Barco-Sushi da Sueli');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

$defaultBooleanAll = new class() extends DishesTable
{
    public string $tableName = 'default-boolean-all-table';

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->label('yes', 'no')
                ->default('all'),
        ];
    }
};

it('applies default boolean filter value "all" on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'all',
        ],
    ]);

    $component->assertSee('Pastel de Nata')
        ->assertSee('Barco-Sushi da Sueli');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultBooleanAll::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanAll::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanAll::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── Select default ────────────────────────────────────────────────────

$defaultSelect = new class() extends DishesTable
{
    public string $tableName = 'default-select-table';

    public function filters(): array
    {
        return [
            Filter::select('category_name', 'category_id')
                ->dataSource(Category::all())
                ->optionValue('category_id')
                ->optionLabel('category_name')
                ->default(1),
        ];
    }
};

it('applies default select filter value on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'select' => [
            'category_id' => 1,
        ],
    ]);

    // category_id 1 = Carnes
    $component->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear a default select filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'select' => [
            'category_id' => 1,
        ],
    ]);

    $component->call('clearFilter', 'category_id');

    expect($component->filters)->toMatchArray([]);

    $component->assertSee('Pastel de Nata');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── Multi Select default ──────────────────────────────────────────────

$defaultMultiSelect = new class() extends DishesTable
{
    public string $tableName = 'default-multi-select-table';

    public function filters(): array
    {
        return [
            Filter::multiSelect('category_name', 'category_id')
                ->dataSource(Category::all())
                ->optionValue('id')
                ->optionLabel('name')
                ->default([1]),
        ];
    }
};

it('applies default multi_select filter value on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'multi_select' => [
            'category_id' => [1],
        ],
    ]);

    // category_id 1 = Carnes
    $component->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultMultiSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultMultiSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultMultiSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

$defaultMultiSelectMultiple = new class() extends DishesTable
{
    public string $tableName = 'default-multi-select-multiple-table';

    public function filters(): array
    {
        return [
            Filter::multiSelect('category_name', 'category_id')
                ->dataSource(Category::all())
                ->optionValue('id')
                ->optionLabel('name')
                ->default([1, 6]),
        ];
    }
};

it('applies default multi_select filter with multiple values on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'multi_select' => [
            'category_id' => [1, 6],
        ],
    ]);

    // category_id 1 = Carnes, 6 = Sobremesas  (Pastel de Nata is cat 6)
    $component->assertSee('Peixada da chef Nábia')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultMultiSelectMultiple::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultMultiSelectMultiple::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultMultiSelectMultiple::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear a default multi_select filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'multi_select' => [
            'category_id' => [1],
        ],
    ]);

    $component->call('clearFilter', 'category_id');

    expect($component->filters)->toMatchArray([]);

    $component->assertSee('Pastel de Nata');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultMultiSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultMultiSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultMultiSelect::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── Input Text default ────────────────────────────────────────────────

$defaultInputText = new class() extends DishesTable
{
    public string $tableName = 'default-input-text-table';

    public function filters(): array
    {
        return [
            Filter::inputText('name')
                ->placeholder('dish_name_placeholder')
                ->default('Pastel de Nata'),
        ];
    }
};

it('applies default input_text filter value on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'input_text' => [
            'name' => 'Pastel de Nata',
        ],
    ]);
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

$defaultInputTextWithOperator = new class() extends DishesTable
{
    public string $tableName = 'default-input-text-operator-table';

    public function filters(): array
    {
        return [
            Filter::inputText('name')
                ->operators()
                ->default(['value' => 'ba', 'operator' => 'contains']),
        ];
    }
};

it('applies default input_text filter with operator on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'input_text' => [
            'name' => 'ba',
        ],
        'input_text_options' => [
            'name' => 'contains',
        ],
    ]);

    $component->assertSee('Barco-Sushi da Sueli')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultInputTextWithOperator::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultInputTextWithOperator::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultInputTextWithOperator::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear a default input_text filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'input_text' => [
            'name' => 'Pastel de Nata',
        ],
    ]);

    $component->call('clearFilter', 'name');

    expect($component->filters)->toMatchArray([]);
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultInputText::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── Number default ────────────────────────────────────────────────────

$defaultNumber = new class() extends DishesTable
{
    public string $tableName = 'default-number-table';

    public function filters(): array
    {
        return [
            Filter::number('price')
                ->thousands("'")
                ->decimal(',')
                ->default(['start' => '10', 'end' => '50']),
        ];
    }
};

it('applies default number filter value on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'number' => [
            'price' => [
                'start' => '10',
                'end' => '50',
            ],
        ],
    ]);

    $component->assertSee('Pastel de Nata')     // price 10.00
        ->assertSee('Peixada da chef Nábia')      // price 20.50
        ->assertSee('Carne Louca')                // price 30.00
        ->assertSee('Bife à Rolê')                // price 40.50
        ->assertSee('Francesinha vegana')          // price 50.00
        ->assertDontSee('Barco-Sushi da Sueli')   // price 5000.00
        ->assertDontSee('борщ');                   // price 5000.00
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultNumber::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultNumber::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultNumber::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

$defaultNumberStartOnly = new class() extends DishesTable
{
    public string $tableName = 'default-number-start-only-table';

    public function filters(): array
    {
        return [
            Filter::number('price')
                ->thousands("'")
                ->decimal(',')
                ->default(['start' => '1000']),
        ];
    }
};

it('applies default number filter with start only on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'number' => [
            'price' => [
                'start' => '1000',
            ],
        ],
    ]);

    $component->assertSee('Barco-Sushi da Sueli')  // price 5000.00
        ->assertSee('борщ')                          // price 5000.00
        ->assertSee('Barco-Sushi Simples')           // price 1500.40
        ->assertDontSee('Pastel de Nata')            // price 10.00
        ->assertDontSee('Carne Louca');              // price 30.00
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultNumberStartOnly::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultNumberStartOnly::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultNumberStartOnly::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear a default number filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'number' => [
            'price' => [
                'start' => '10',
                'end' => '50',
            ],
        ],
    ]);

    $component->call('clearFilter', 'price');

    expect($component->filters)->toMatchArray([]);

    $component->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Pastel de Nata');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultNumber::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultNumber::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultNumber::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── Date/Datetime default ─────────────────────────────────────────────

$defaultDatetime = new class() extends DishesTable
{
    public string $tableName = 'default-datetime-table';

    public function filters(): array
    {
        return [
            Filter::datetimepicker('produced_at')
                ->default([
                    'start' => '2021-01-01 00:00:00',
                    'end' => '2021-03-03 23:59:59',
                    'formatted' => '2021-01-01 to 2021-03-03',
                ]),
        ];
    }
};

it('applies default datetime filter value on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'datetime' => [
            'produced_at' => [
                'start' => '2021-01-01 00:00:00',
                'end' => '2021-03-03 23:59:59',
                'formatted' => '2021-01-01 to 2021-03-03',
            ],
        ],
    ]);
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultDatetime::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultDatetime::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultDatetime::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

$defaultDate = new class() extends DishesTable
{
    public string $tableName = 'default-date-table';

    public function filters(): array
    {
        return [
            Filter::datepicker('produced_at')
                ->default([
                    'start' => '2021-01-01',
                    'end' => '2021-05-05',
                    'formatted' => '2021-01-01 to 2021-05-05',
                ]),
        ];
    }
};

it('applies default date filter value on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'date' => [
            'produced_at' => [
                'start' => '2021-01-01',
                'end' => '2021-05-05',
                'formatted' => '2021-01-01 to 2021-05-05',
            ],
        ],
    ]);
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultDate::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultDate::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultDate::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear a default datetime filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->not->toBeEmpty();

    $component->call('clearFilter', 'produced_at');

    expect($component->filters)->toMatchArray([]);
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultDatetime::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultDatetime::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultDatetime::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── Enabled filters tracking ─────────────────────────────────────────

it('registers default filters in enabledFilters', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->enabledFilters)
        ->toBeArray()
        ->not->toBeEmpty();

    $fields = collect($component->enabledFilters)->pluck('field')->all();

    expect($fields)->toContain('in_stock');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── clearAllFilters resets defaults ───────────────────────────────────

$defaultMultipleFilters = new class() extends DishesTable
{
    public string $tableName = 'default-multiple-filters-table';

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->label('yes', 'no')
                ->default('true'),

            Filter::inputText('name')
                ->default('Pastel'),
        ];
    }
};

it('clearAllFilters resets all default filters', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->not->toBeEmpty();

    $component->call('clearAllFilters');

    expect($component->filters)->toMatchArray([]);
    expect($component->enabledFilters)->toMatchArray([]);
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultMultipleFilters::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultMultipleFilters::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultMultipleFilters::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── Multiple default filters applied together ─────────────────────────

it('applies multiple default filters simultaneously on mount', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
        'input_text' => [
            'name' => 'Pastel',
        ],
    ]);

    $fields = collect($component->enabledFilters)->pluck('field')->all();

    expect($fields)->toContain('in_stock')
        ->toContain('name');
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$defaultMultipleFilters::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultMultipleFilters::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultMultipleFilters::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

// ── No default value means no filter applied ──────────────────────────

$noDefaultFilter = new class() extends DishesTable
{
    public string $tableName = 'no-default-filter-table';

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->label('yes', 'no'),

            Filter::inputText('name'),
        ];
    }
};

it('does not apply filters when no default value is set', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([]);
    expect($component->enabledFilters)->toBeEmpty();
})->group('filters', 'filterDefaultValue')
    ->with([
        'tailwind' => [$noDefaultFilter::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$noDefaultFilter::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$noDefaultFilter::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);
