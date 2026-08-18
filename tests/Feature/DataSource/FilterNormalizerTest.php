<?php

use PowerComponents\Turbine\DataSource\Support\FilterNormalizer;

uses()->group('datasource', 'filter-normalizer');

it('keeps scalar filter values untouched', function () {
    expect(FilterNormalizer::normalize(['amount' => 5]))->toBe(['amount' => 5]);
});

it('rebuilds multi_select arrays', function () {
    expect(FilterNormalizer::normalize(['category_id' => [1, 2]]))->toBe(['category_id' => [1, 2]]);
});

it('rebuilds number and date ranges', function () {
    expect(FilterNormalizer::normalize(['price' => ['start' => 1, 'end' => 10]]))
        ->toBe(['price' => ['start' => 1, 'end' => 10]]);
});

it('keeps relation paths intact so collection and database filters match', function () {
    expect(FilterNormalizer::normalize(['user' => ['roles' => [1, 2]]]))
        ->toBe(['user.roles' => [1, 2]]);
});
