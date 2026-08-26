<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\Turbine\Components\SetUp\Responsive;

uses()->group('responsive');

class ResponsiveHarness extends PowerGridComponent
{
    public string $tableName = 'responsive-table';

    /** @var list<string> */
    public array $fixedColumns = [];

    public bool $withCheckbox = false;

    public bool $withInline = false;

    public bool $withDetail = false;

    public bool $withJoinName = false;

    public bool $withIdColumn = false;

    public bool $withActions = true;

    public function filterPosition(): string
    {
        return $this->withInline ? 'inline' : parent::filterPosition();
    }

    public function setUp(): array
    {
        if ($this->withCheckbox) {
            $this->showCheckBox();
        }

        $responsive = PowerGrid::responsive();

        if ($this->fixedColumns !== []) {
            $responsive->fixedColumns(...$this->fixedColumns);
        }

        $setUp = [$responsive];

        if ($this->withDetail) {
            $setUp[] = PowerGrid::detail()
                ->view('livewire-powergrid::tests.detail')
                ->showCollapseIcon();
        }

        return $setUp;
    }

    public function datasource()
    {
        return collect([['id' => 1, 'name' => 'Dish 1']]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()->add('id')->add('name');
    }

    public function columns(): array
    {
        $columns = [];

        if ($this->withIdColumn) {
            $columns[] = Column::make('Id', 'id');
        }

        $columns[] = $this->withJoinName
            ? Column::make('Name', 'name', 'dishes.name')
            : Column::make('Name', 'name');

        if ($this->withActions) {
            $columns[] = Column::action('Actions');
        }

        return $columns;
    }

    public function filters(): array
    {
        return $this->withInline ? [Filter::inputText('name')] : [];
    }
}

it('marks a column fixed when fixedColumns() uses the field and the dataField differs', function () {
    $html = Livewire::test(ResponsiveHarness::class, [
        'tableName' => 'responsive-fixed-name',
        'fixedColumns' => ['checkbox', 'name', 'actions'],
        'withJoinName' => true,
    ])->html();

    expect($html)
        ->toContain('x-data="pgResponsive"')
        ->toContain('data-pg-row-id="1"')
        ->toContain('data-expand-for="1"')
        ->toContain('wire:key="expand-1"')
        ->toMatch('/data-column="dishes\.name"[^>]*\sfixed/')
        ->toMatch('/data-column="actions"[^>]*\sfixed/');
});

it('keeps the action column fixed by default even when Column::action() has an empty field', function () {
    $html = Livewire::test(ResponsiveHarness::class, [
        'tableName' => 'responsive-default-actions',
        'withIdColumn' => true,
    ])->html();

    expect($html)
        ->toMatch('/data-column="id"[^>]*\sfixed/')
        ->toMatch('/data-column="actions"[^>]*\sfixed/')
        ->not->toMatch('/data-column="name"[^>]*\sfixed/');
});

it('renders expand markup next to the detail row', function () {
    $html = Livewire::test(ResponsiveHarness::class, [
        'tableName' => 'responsive-with-detail',
        'withDetail' => true,
        'withActions' => false,
    ])->html();

    expect($html)
        ->toContain('data-table-name="responsive-with-detail"')
        ->toContain('data-row-id="1"')
        ->toContain('data-expand-for="1"')
        ->toContain('data-pg-row-id="1"')
        ->toMatch('/data-row-id="1"[\s\S]*data-expand-for="1"/');
});

it('reserves an inline-filter spacer for the responsive toggle column', function () {
    Config::set('livewire-powergrid.filter', 'inline');

    $html = Livewire::test(ResponsiveHarness::class, [
        'tableName' => 'responsive-inline',
        'withInline' => true,
        'withActions' => false,
    ])->html();

    expect($html)
        ->toContain('data-pg-inline-filters')
        ->toMatch('/data-pg-inline-filters[\s\S]*<td[^>]*>[\s\S]*<\/td>[\s\S]*column-filter-name/');
});

it('matches field and dataField on Responsive::isColumnFixed', function () {
    $column = Column::make('Name', 'name', 'dishes.name');

    expect(Responsive::isColumnFixed($column, ['name']))->toBeTrue()
        ->and(Responsive::isColumnFixed($column, ['dishes.name']))->toBeTrue()
        ->and(Responsive::isColumnFixed($column, ['id']))->toBeFalse()
        ->and(Responsive::isColumnFixed($column->fixedOnResponsive(), ['id']))->toBeTrue();
});

it('matches sortOrder by field or dataField', function () {
    $column = Column::make('Name', 'name', 'dishes.name');

    expect(Responsive::columnSortOrder($column, ['name' => 2]))->toBe(2)
        ->and(Responsive::columnSortOrder($column, ['dishes.name' => 4]))->toBe(4)
        ->and(Responsive::columnSortOrder($column, ['id' => 1]))->toBeNull();
});
