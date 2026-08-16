<?php

use PowerComponents\Turbine\Components\SetUp\Footer;

it('adds custom perPage value when not in default array', function () {
    $footer = new Footer();
    $result = $footer->showPerPage(30);

    expect($result->perPage)->toBe(30)
        ->and($result->perPageValues)->toBe([10, 25, 30, 50, 100, 0]);
});

it('accepts custom perPageValues', function () {
    $footer = new Footer();
    $perPageValues = [5, 15, 30, 50];
    $result = $footer->showPerPage(15, $perPageValues);

    expect($result->perPage)->toBe(15)
        ->and($result->perPageValues)->toBe([5, 15, 30, 50]);
});

it('adds custom perPage value when not in custom perPageValues without zero', function () {
    $footer = new Footer();
    $perPageValues = [5, 15, 50];
    $result = $footer->showPerPage(30, $perPageValues);

    expect($result->perPage)->toBe(30)
        ->and($result->perPageValues)->toBe([5, 15, 30, 50]);
});

it('adds custom perPage value when not in custom perPageValues with zero', function () {
    $footer = new Footer();
    $perPageValues = [5, 15, 50, 0];
    $result = $footer->showPerPage(30, $perPageValues);

    expect($result->perPage)->toBe(30)
        ->and($result->perPageValues)->toBe([5, 15, 30, 50, 0]);
});
