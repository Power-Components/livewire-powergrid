<?php

use Illuminate\Support\Collection;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Themes\{Flux, Tailwind, Theme};
use PowerComponents\Turbine\Components\SetUp\HeaderElement;

$table = new class() extends PowerGridComponent
{
    public string $tableName = 'test-header-elements';

    public string $primaryKey = 'id';

    /** @var Closure(): array<int, mixed> */
    public static Closure $definitions;

    /** @var Closure(): ?Theme */
    public static ?Closure $themeResolver = null;

    public function setUp(): array
    {
        return (self::$definitions)();
    }

    public function template(): ?Theme
    {
        return self::$themeResolver ? (self::$themeResolver)() : null;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()->add('id')->add('name');
    }

    public function columns(): array
    {
        return [Column::make('Id', 'id'), Column::make('Name', 'name')];
    }

    public function datasource(): ?Collection
    {
        return collect([['id' => 1, 'name' => 'Luan']]);
    }
};

$tableClass = $table::class;

beforeEach(function () use ($tableClass) {
    config()->set('livewire-powergrid.theme', Tailwind::class);

    $tableClass::$definitions = fn () => [];
    $tableClass::$themeResolver = null;
});

it('falls back to the package icon and the lang title', function () use ($tableClass) {
    $tableClass::$definitions = fn () => [PowerGrid::header()->showToggleColumns()];

    Livewire::test($tableClass)
        ->assertSeeHtml('aria-label="'.trans('livewire-powergrid::datatable.buttons.toggle_columns').'"')
        ->assertSeeHtml('w-5 h-5 shrink-0 text-zinc-500 dark:text-zinc-300');
});

it('renders the user icon component and title for the toggle columns button', function () use ($tableClass) {
    $tableClass::$definitions = fn () => [
        PowerGrid::header()->showToggleColumns(fn (HeaderElement $element) => $element
            ->icon('livewire-powergrid::icons.trash', ['class' => 'custom-icon-class'])
            ->title('My Columns')
            ->showLabel()),
    ];

    Livewire::test($tableClass)
        ->assertSeeHtml('custom-icon-class')
        ->assertSeeHtml('My Columns');
});

it('translates a title given as a lang key', function () use ($tableClass) {
    $tableClass::$definitions = fn () => [
        PowerGrid::header()->showToggleColumns(fn (HeaderElement $element) => $element
            ->title('livewire-powergrid::datatable.buttons.close')),
    ];

    Livewire::test($tableClass)
        ->assertSeeHtml('aria-label="'.trans('livewire-powergrid::datatable.buttons.close').'"');
});

it('renders no icon when the icon is disabled', function () use ($tableClass) {
    $tableClass::$definitions = fn () => [
        PowerGrid::header()->showSoftDeletes(config: fn (HeaderElement $element) => $element->withoutIcon()),
    ];

    Livewire::test($tableClass)
        ->assertSeeHtml('aria-label="'.trans('livewire-powergrid::datatable.buttons.soft_deletes').'"')
        ->assertDontSeeHtml('m14.74 9-.346 9m-4.788 0L9.26 9');
});

it('lets the theme define the button classes and the default icon', function () use ($tableClass) {
    $tableClass::$definitions = fn () => [PowerGrid::header()->showToggleColumns()];
    $tableClass::$themeResolver = fn () => Tailwind::make()->merge([
        'header' => [
            'toggle_columns' => [
                'button' => 'themed-toggle-button',
                'icon' => 'livewire-powergrid::icons.trash',
                'icon_class' => 'themed-icon-class',
            ],
        ],
    ]);

    Livewire::test($tableClass)
        ->assertSeeHtml('themed-toggle-button')
        ->assertSeeHtml('themed-icon-class');
});

it('gives the user setUp precedence over the theme token', function () use ($tableClass) {
    $tableClass::$definitions = fn () => [
        PowerGrid::header()->showToggleColumns(fn (HeaderElement $element) => $element
            ->icon('livewire-powergrid::icons.copy', ['class' => 'user-icon-class'])),
    ];
    $tableClass::$themeResolver = fn () => Tailwind::make()->merge([
        'header' => [
            'toggle_columns' => [
                'icon' => 'livewire-powergrid::icons.trash',
                'icon_class' => 'themed-icon-class',
            ],
        ],
    ]);

    $element = Livewire::test($tableClass)->instance()->headerElement('toggleColumns');

    expect($element['icon'])->toBe('livewire-powergrid::icons.copy')
        ->and($element['iconAttributes'])->toBe(['class' => 'user-icon-class']);
});

it('lets the user swap the element view', function () use ($tableClass) {
    $tableClass::$definitions = fn () => [
        PowerGrid::header()->showToggleColumns(fn (HeaderElement $element) => $element
            ->view('livewire-powergrid::components.themes.daisyui.header.toggle-columns')),
    ];

    expect(Livewire::test($tableClass)->instance()->headerElement('toggleColumns')['view'])
        ->toBe('livewire-powergrid::components.themes.daisyui.header.toggle-columns');
});

it('configures the filter builder trigger through the fluent setUp api', function () use ($tableClass) {
    config()->set('livewire-powergrid.theme', Flux::class);

    $tableClass::$definitions = fn () => [
        PowerGrid::filterBuilder()->title('My Filters')->icon('funnel'),
    ];

    $element = Livewire::test($tableClass)->instance()->headerElement('filterBuilder');

    expect($element['title'])->toBe('My Filters')
        ->and($element['icon'])->toBe('funnel')
        ->and($element['isComponentPath'])->toBeFalse()
        ->and($element['showLabel'])->toBeFalse();
});

it('configures the export trigger through the fluent setUp api', function () use ($tableClass) {
    $tableClass::$definitions = fn () => [
        PowerGrid::exportable('report')->title('Download')->icon('livewire-powergrid::icons.copy'),
    ];

    $element = Livewire::test($tableClass)->instance()->headerElement('export');

    expect($element['title'])->toBe('Download')
        ->and($element['icon'])->toBe('livewire-powergrid::icons.copy')
        ->and($element['isComponentPath'])->toBeTrue();
});
