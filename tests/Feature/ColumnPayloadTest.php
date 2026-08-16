<?php

use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Support\Synth\PowerGridWireableSynth;

it('excludes non-serializable and rebuildable keys from the Livewire payload', function () {
    $column = Column::make('Name', 'name');

    $column->filters = ['some' => 'resolved-datasource-options'];

    $payload = PowerGridWireableSynth::unwrapForValidation($column);

    expect($payload)
        ->not->toHaveKey('filters')
        ->not->toHaveKey('sortCallback')
        ->not->toHaveKey('rawQueries')
        ->not->toHaveKey('summaryCallbacks')
        ->toHaveKey('field')
        ->toHaveKey('title')
        ->toHaveKey('pluginData');
});
