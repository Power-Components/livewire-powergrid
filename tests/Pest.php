<?php

use PowerComponents\LivewirePowerGrid\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
