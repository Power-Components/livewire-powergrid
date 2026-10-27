<?php

use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\{Cache, Cookie};
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

it('scopes the lite cache persist key per user', function () {
    config()->set('livewire-powergrid.persist_driver', 'cache');

    $this->actingAs(new GenericUser(['id' => 7]));

    Livewire::test(LitePersistComponent::class)
        ->call('sortBy', 'name');

    expect(Cache::get('pg_lite:LitePersistComponent-u7'))->toContain('name')
        ->and(Cache::get('pg_lite:LitePersistComponent'))->toBeNull();
});

class LiteAllowlistSortComponent extends LitePersistComponent
{
    public array $persist = [];

    public array $sortableFields = ['name'];
}

it('ignores lite sort fields outside the allowlist or with bad direction', function () {
    Livewire::test(LiteAllowlistSortComponent::class)
        ->call('sortBy', 'password')
        ->assertSet('sortField', '')
        ->call('sortBy', 'name); drop table users;--')
        ->assertSet('sortField', '')
        ->call('sortBy', 'name', 'sideways')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc');
});
