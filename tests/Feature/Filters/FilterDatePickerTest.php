<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$component = new class() extends DishesTable
{
    public string $tableName = 'filter-date-picker-test';

    public function filters(): array
    {
        return [
            Filter::datepicker('produced_at'),
        ];
    }
};

it('should dispatch "pg:datePicker-{tableName}" with date format', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->dispatch(
            'pg:datePicker-filter-date-picker-test',
            selectedDates: ['2024-11-06', '2021-02-02'],
            field: 'produced_at',
            dateStr: '2021-01-01 to 2021-02-02',
            label: 'Produced At',
            type: 'datetime',
            timezone: 'UTC',
            dateFormat: 'Y-m-d'
        )
        ->assertSee('Peixada');

    expect($component->filters)
        ->toBe([
            'datetime' => [
                'produced_at' => [
                    'start' => 'Fri Jan 01 2021 00:00:00 GMT+0000',
                    'end' => 'Tue Feb 02 2021 23:59:59 GMT+0000',
                    'formatted' => '2021-01-01 to 2021-02-02',
                ],
            ],
        ]);
})->with('filterComponent');

it('should filter "pg:datePicker-{tableName}" with date format', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('Peixada')
        ->dispatch(
            'pg:datePicker-filter-date-picker-test',
            selectedDates: ['2021-03-03', '2021-05-05'],
            field: 'produced_at',
            dateStr: '2021-03-03 to 2021-05-05',
            label: 'Produced At',
            type: 'date',
            timezone: 'UTC',
            dateFormat: 'Y-m-d'
        )
        ->assertDontSee('Peixada');
})->with('filterComponent');

it('should filter "pg:datePicker-{tableName}" with datetime format', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('Peixada', 'Pastel de Nata')
        ->dispatch(
            'pg:datePicker-filter-date-picker-test',
            selectedDates: ['2021-01-01', '2021-02-02'],
            field: 'produced_at',
            dateStr: '2021-01-01 00:00:00 to 2021-02-02 04:00:00',
            label: 'Produced At',
            type: 'datetime',
            timezone: 'UTC',
            dateFormat: 'Y-m-d H:i:s'
        )
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada');
})->with('filterComponent');

it('ignores date picker change when only one date is selected', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->dispatch(
            'pg:datePicker-filter-date-picker-test',
            selectedDates: ['2024-11-06'],
            field: 'produced_at',
            dateStr: '2024-11-06',
            label: 'Produced At',
            type: 'date'
        );

    expect($component->filters)->toBeEmpty();
})->with('filterComponent');

it('should dispatch "pg:datePicker-{tableName}" with datetime format', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->dispatch(
            'pg:datePicker-filter-date-picker-test',
            selectedDates: ['2024-11-06', '2021-02-02'],
            field: 'produced_at',
            dateStr: '2021-01-01 03:00:00 to 2021-02-02 05:00:00',
            label: 'Produced At',
            type: 'datetime',
            timezone: 'UTC',
            dateFormat: 'Y-m-d H:i:s'
        )
        ->assertSee('Peixada');

    expect($component->filters)
        ->toBe([
            'datetime' => [
                'produced_at' => [
                    'start' => 'Fri Jan 01 2021 03:00:00 GMT+0000',
                    'end' => 'Tue Feb 02 2021 05:00:00 GMT+0000',
                    'formatted' => '2021-01-01 03:00:00 to 2021-02-02 05:00:00',
                ],
            ],
        ]);
})->with('filterComponent');

it('handles invalid date format gracefully', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->dispatch(
            'pg:datePicker-filter-date-picker-test',
            selectedDates: ['invalid-date', 'invalid-date-2'],
            field: 'produced_at',
            dateStr: 'invalid-date to invalid-date-2',
            label: 'Produced At',
            type: 'date'
        );
})->with('filterComponent');

it('clears date filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->dispatch(
            'pg:datePicker-filter-date-picker-test',
            selectedDates: ['2021-01-01', '2021-01-02'],
            field: 'produced_at',
            dateStr: '2021-01-01 to 2021-01-02',
            label: 'Produced At',
            type: 'date'
        );

    $component->call('clearFilter', 'produced_at');
    expect($component->filters)->toBeEmpty();
})->with('filterComponent');

dataset('filterComponent', [
    'tailwind -> id' => [$component::class, (object) ['theme' => Tailwind::class]],
]);
