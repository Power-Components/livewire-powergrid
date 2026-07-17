<?php

use PowerComponents\LivewirePowerGrid\Components\SetUp\{Cache, Detail};

uses()->group('setup');

it('builds the cache setup component through every setter', function () {
    $cache = (new Cache())
        ->disabled()
        ->customTag('my-tag')
        ->prefix('pg_')
        ->ttl(600);

    expect($cache->enabled)->toBeFalse()
        ->and($cache->tag)->toBe('my-tag')
        ->and($cache->prefix)->toBe('pg_')
        ->and($cache->ttl)->toBe(600);

    // Wireable round-trip
    $wired = $cache->toLivewire();
    expect($wired)->toBeArray()
        ->and($wired['tag'])->toBe('my-tag')
        ->and(Cache::fromLivewire($wired))->toBe($wired);
});

it('builds the detail setup component through every setter', function () {
    $detail = (new Detail())
        ->view('components.detail')
        ->options(['legacy' => true])
        ->params(['foo' => 'bar'])
        ->showCollapseIcon('custom-icon')
        ->collapseOthers();

    expect($detail->view)->toBe('components.detail')
        ->and($detail->options)->toBe(['foo' => 'bar']) // params overwrites options
        ->and($detail->showCollapseIcon)->toBeTrue()
        ->and($detail->viewIcon)->toBe('custom-icon')
        ->and($detail->collapseOthers)->toBeTrue();

    $wired = $detail->toLivewire();
    expect($wired)->toBeArray()
        ->and($wired['view'])->toBe('components.detail')
        ->and(Detail::fromLivewire($wired))->toBe($wired);
});
