<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

uses()->group('snapshot');

class SnapshotHarness extends PowerGridComponent
{
    public string $tableName = 'snapshot-rebind';

    public function setUp(): array
    {
        return [
            PowerGrid::exportable('dishes-export')
                ->type('csv'),
            PowerGrid::footer()->showPerPage(10),
        ];
    }

    public function datasource()
    {
        return collect([
            ['id' => 1, 'name' => 'Pastel', 'secret' => 'hidden-value'],
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()->add('id')->add('name')->add('secret');
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Secret', 'secret')->hidden(),
        ];
    }
}

function columnByField(mixed $columns, string $field): mixed
{
    return collect($columns)->first(fn ($column) => data_get($column, 'field') === $field);
}

it('drops a tampered column title on the next request', function () {
    $test = Livewire::test(SnapshotHarness::class)
        ->set('columns.0.title', '<img src=x onerror=alert(1)>')
        ->call('$refresh');

    expect(data_get($test->get('columns.0'), 'title'))->toBe('Name')
        ->and($test->html())->not->toContain('<img src=x onerror=alert(1)>');
});

it('drops a customContent view injected through the snapshot', function () {
    $test = Livewire::test(SnapshotHarness::class)
        ->set('columns.0.customContent', [
            'view' => 'livewire-powergrid::tests.svg',
            'params' => [],
        ]);

    expect(data_get($test->get('columns.0'), 'customContent'))->toBeEmpty();
});

it('does not un-hide a forceHidden column from the snapshot', function () {
    $test = Livewire::test(SnapshotHarness::class)
        ->set('columns.1.hidden', false)
        ->set('columns.1.forceHidden', false)
        ->call('$refresh');

    $secret = columnByField($test->get('columns'), 'secret');

    expect(data_get($secret, 'hidden'))->toBeTrue()
        ->and(data_get($secret, 'forceHidden'))->toBeTrue();
});

it('keeps a legitimate hidden flag from toggle columns', function () {
    $test = Livewire::test(SnapshotHarness::class)
        ->set('draftColumns.name', false)
        ->call('applyColumns');

    expect(data_get(columnByField($test->get('columns'), 'name'), 'hidden'))->toBeTrue();

    $test->call('$refresh');

    expect(data_get(columnByField($test->get('columns'), 'name'), 'hidden'))->toBeTrue();
});

it('restores exportable.fileName from the server declaration', function () {
    $test = Livewire::test(SnapshotHarness::class)
        ->set('setUp.exportable.fileName', '../../public/pwned')
        ->set('setUp.exportable.jobClass', 'App\\Jobs\\EvilJob')
        ->call('$refresh');

    expect(data_get($test->get('setUp'), 'exportable.fileName'))->toBe('dishes-export')
        ->and(data_get($test->get('setUp'), 'exportable.jobClass'))->toBe('');
});

it('rejects a perPage value that is not in the footer allowlist', function () {
    $test = Livewire::test(SnapshotHarness::class)
        ->set('setUp.footer.perPage', 11);

    expect(data_get($test->get('setUp'), 'footer.perPage'))->toBe(10);
});

it('keeps an allowed perPage value across requests', function () {
    $test = Livewire::test(SnapshotHarness::class)
        ->set('setUp.footer.perPage', 25)
        ->call('$refresh');

    expect(data_get($test->get('setUp'), 'footer.perPage'))->toBe(25);
});
