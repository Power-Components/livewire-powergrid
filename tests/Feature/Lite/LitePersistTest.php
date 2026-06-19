<?php

use Illuminate\Support\Facades\Cookie;
use Livewire\{Component, Livewire};
use PowerComponents\LivewirePowerGrid\Lite\Traits\{WithPersist, WithSorting};

uses()->group('lite', 'persist');

class LitePersistComponent extends Component
{
    use WithPersist;
    use WithSorting;

    public array $persist = ['sorting'];

    public function render()
    {
        return <<<'BLADE'
        <div>{{ $sortField }}</div>
        BLADE;
    }
}

it('queues the lite persist cookie with a future expiry', function () {
    config()->set('livewire-powergrid.persist_driver', 'cookies');

    Livewire::test(LitePersistComponent::class)
        ->call('sortBy', 'name');

    $cookie = collect(Cookie::getQueuedCookies())
        ->first(fn ($cookie) => str_starts_with($cookie->getName(), 'pg_lite:'));

    expect($cookie)->not->toBeNull()
        ->and($cookie->getExpiresTime())->toBeGreaterThan(time());
});
