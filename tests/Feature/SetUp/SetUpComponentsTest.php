<?php

use PowerComponents\LivewirePowerGrid\Support\Synth\PowerGridWireableSynth;
use PowerComponents\Turbine\Components\SetUp\{Cache, Detail};

uses()->group('setup');

function wireRoundTrip(object $definition): object
{
    /** @var PowerGridWireableSynth $synth */
    $synth = (new ReflectionClass(PowerGridWireableSynth::class))->newInstanceWithoutConstructor();

    [$data, $meta] = $synth->dehydrate($definition, fn ($key, $value) => $value);

    return $synth->hydrate($data, $meta, fn ($key, $value) => $value);
}

it('builds the cache setup component through every setter', function () {
    $cache = (new Cache())
        ->disabled()
        ->customTag('my-tag')
        ->prefix('turbine_')
        ->ttl(600);

    expect($cache->enabled)->toBeFalse()
        ->and($cache->tag)->toBe('my-tag')
        ->and($cache->prefix)->toBe('turbine_')
        ->and($cache->ttl)->toBe(600);

    $restored = wireRoundTrip($cache);
    expect($restored)->toBeInstanceOf(Cache::class)
        ->and($restored->tag)->toBe('my-tag')
        ->and($restored->enabled)->toBeFalse()
        ->and($restored->ttl)->toBe(600);
});

it('builds the detail setup component through every setter', function () {
    $detail = (new Detail())
        ->view('components.detail')
        ->options(['legacy' => true])
        ->params(['foo' => 'bar'])
        ->showCollapseIcon('custom-icon')
        ->singleExpand();

    expect($detail->view)->toBe('components.detail')
        ->and($detail->options)->toBe(['foo' => 'bar']) // params overwrites options
        ->and($detail->showCollapseIcon)->toBeTrue()
        ->and($detail->viewIcon)->toBe('custom-icon')
        ->and($detail->singleExpand)->toBeTrue();

    $restored = wireRoundTrip($detail);
    expect($restored)->toBeInstanceOf(Detail::class)
        ->and($restored->view)->toBe('components.detail')
        ->and($restored->singleExpand)->toBeTrue();
});

it('keeps collapseOthers() as a deprecated alias of singleExpand()', function () {
    $detail = (new Detail())->collapseOthers();

    expect($detail->singleExpand)->toBeTrue();
});
