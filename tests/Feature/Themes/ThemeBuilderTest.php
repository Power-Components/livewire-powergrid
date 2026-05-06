<?php

use PowerComponents\LivewirePowerGrid\Themes\Components\{Header, Table, ThemeBuilder};

it('constructs the theme array correctly using ThemeBuilder', function () {
    $struct = ThemeBuilder::make('my-theme')
        ->table(fn (Table $table) => $table
            ->view('my-table-base')
            ->layout(fn ($layout) => $layout
                ->table('my-table-base')
                ->thead('my-thead')
                ->tr('my-tr')
            )
        )
        ->toArray();

    expect($struct)->toBe([
        'name' => 'my-theme',
        'table' => [
            'view' => 'my-table-base',
            'layout' => [
                'table' => 'my-table-base',
                'thead' => 'my-thead',
                'tr' => 'my-tr',
            ],
        ],
    ]);
});

it('correctly snake_cases property keys', function () {
    $struct = ThemeBuilder::make('test')
        ->header(fn (Header $header) => $header
            ->searchBox(fn ($box) => $box->relativeMain('relative'))
        )
        ->toArray();

    expect($struct['header'])->toHaveKey('search_box')
        ->and($struct['header']['search_box'])->toHaveKey('relative_main');
});
