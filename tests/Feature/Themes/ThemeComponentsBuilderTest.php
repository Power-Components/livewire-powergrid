<?php

use PowerComponents\LivewirePowerGrid\Themes\Components\{Body, Component, Footer, Pagination, Table, Td, Tr};

uses()->group('themes');

it('builds the table theme component through every setter', function () {
    $table = (new Table())
        ->view('table')
        ->viewLayout('layout')
        ->viewHeader('header')
        ->viewRow('row')
        ->viewCols('cols')
        ->viewThEmpty('th-empty')
        ->viewInlineFilters('inline-filters')
        ->viewCheckboxAll('checkbox-all')
        ->viewCheckboxRow('checkbox-row')
        ->viewRadioRow('radio-row')
        ->layout(['container' => 'c'])                                   // array branch
        ->body(fn (Body $body) => $body->tr('tr-class')->td('td-class')) // closure branch
        ->checkbox(['base' => 'cb'])
        ->radio(fn ($radio) => $radio);

    // toArray() snake_cases the keys
    expect($table->toArray())->toMatchArray([
        'view' => 'table',
        'view_layout' => 'layout',
        'view_header' => 'header',
        'view_row' => 'row',
        'view_cols' => 'cols',
        'view_th_empty' => 'th-empty',
        'view_inline_filters' => 'inline-filters',
        'view_checkbox_all' => 'checkbox-all',
        'view_checkbox_row' => 'checkbox-row',
        'view_radio_row' => 'radio-row',
    ])->and($table->toArray())->toHaveKeys(['layout', 'body', 'checkbox', 'radio']);
});

it('builds the generic theme component through every setter', function () {
    $component = (new Component())
        ->view('v')->base('b')->input('i')->select('s')->th('th')->label('l')
        ->clickable('c')->error('e')->container('ct')->relativeMain('rm')
        ->iconSearchWrapper('isw')->iconCloseWrapper('icw')->iconClose('ic')->iconSearch('is');

    expect($component->toArray())->toMatchArray([
        'view' => 'v', 'base' => 'b', 'input' => 'i', 'select' => 's', 'th' => 'th',
        'label' => 'l', 'clickable' => 'c', 'error' => 'e', 'container' => 'ct',
        'relative_main' => 'rm', 'icon_search_wrapper' => 'isw', 'icon_close_wrapper' => 'icw',
        'icon_close' => 'ic', 'icon_search' => 'is',
    ]);
});

it('builds the td theme component', function () {
    $td = (new Td())->base('td-base')->actionsWrapper('actions');

    expect($td->toArray())->toMatchArray(['base' => 'td-base', 'actions_wrapper' => 'actions']);
});

it('builds the body theme component with string and closure children', function () {
    $body = (new Body())->tr('tr-class')->td('td-class');

    expect($body->toArray())->toHaveKeys(['tr', 'td']);
});

it('builds the tr theme component', function () {
    $tr = (new Tr())->base('tr-base')->responsive('resp')->responsiveToggleIcon('icon');

    expect($tr->toArray())->toMatchArray([
        'base' => 'tr-base',
        'responsive' => 'resp',
        'responsive_toggle_icon' => 'icon',
    ]);
});

it('builds the pagination theme component from constructor and setter', function () {
    expect((new Pagination('pag-view'))->toArray())->toMatchArray(['view' => 'pag-view'])
        ->and((new Pagination())->view('other')->toArray())->toMatchArray(['view' => 'other']);
});

it('builds the footer theme component', function () {
    $footer = (new Footer())
        ->view('footer')
        ->layout(['base' => 'f'])
        ->pagination('pagination-view');

    expect($footer->toArray())->toHaveKeys(['view', 'layout', 'pagination']);
});
