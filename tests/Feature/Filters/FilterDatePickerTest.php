<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind};

$component = new class () extends DishesTable {
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
            timezone: 'UTC'
        )
        ->assertSee('Peixada');

    expect($component->filters)
        ->toBe([
            'datetime' => [
                'produced_at' => [
                    'start'     => 'Fri Jan 01 2021 00:00:00 GMT+0000',
                    'end'       => 'Tue Feb 02 2021 23:59:59 GMT+0000',
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
            timezone: 'UTC'
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
            timezone: 'UTC'
        )
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada');
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
            timezone: 'UTC'
        )
        ->assertSee('Peixada');

    expect($component->filters)
        ->toBe([
            'datetime' => [
                'produced_at' => [
                    'start'     => 'Fri Jan 01 2021 03:00:00 GMT+0000',
                    'end'       => 'Tue Feb 02 2021 05:00:00 GMT+0000',
                    'formatted' => '2021-01-01 03:00:00 to 2021-02-02 05:00:00',
                ],
            ],
        ]);
})->with('filterComponent');

dataset('filterComponent', [
    'tailwind -> id'  => [$component::class, (object) ['theme' => Tailwind::class]],
    'bootstrap -> id' => [$component::class, (object) ['theme' => Bootstrap5::class]],
]);
