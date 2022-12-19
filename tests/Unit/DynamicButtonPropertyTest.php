<?php

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Helpers\Actions;
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind};

beforeEach(function () {
    Button::macro('icon', function (string $name) {
        $this->dynamicProperties['icon'] = $name;

        return $this;
    });
});

it('properly handles macroable button method', function (string $theme) {
    $button = Button::add('edit')
        ->class('text-center')
        ->icon('fa-user');

    $actionClass = new Actions(
        $button,
        new stdClass(),
        'id',
        new $theme(),
    );

    expect($actionClass->getDynamicProperty('icon'))
        ->toBe('fa-user');
})->with('dynamic-action')->group('dynamic-action');

dataset('dynamic-action', [
    'tailwind'  => [Tailwind::class],
    'bootstrap' => [Bootstrap5::class],
]);
