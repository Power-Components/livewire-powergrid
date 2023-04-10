<?php

use Illuminate\Pagination\LengthAwarePaginator;

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Cache;

use PowerComponents\LivewirePowerGrid\Tests\DishesSetUpTable;

it('show includeViewOnTop/Bottom - Header', function () {
    livewire(DishesSetUpTable::class, ['testHeader' => true])
        ->assertSeeHtmlInOrder([
            '<div>Included By Header Top</div>',
            'Pastel de Nata',
        ])
        ->assertSeeHtml('<div>Included By Header Bottom</div>')
        ->assertDontSeeHtml('<div>Included By Footer Top</div>')
        ->assertDontSeeHtml('<div>Included By Footer Bottom</div>');
});

it('show includeViewOnTop/Bottom - Footer', function () {
    livewire(DishesSetUpTable::class, ['testFooter' => true])
        ->assertSeeHtmlInOrder([
            'Pastel de Nata',
            '<div>Included By Footer Top</div>',
            '<div>Included By Footer Bottom</div>',
        ])
        ->assertDontSeeHtml('<div>Included By Header Top</div>')
        ->assertDontSeeHtml('<div>Included By Header Bottom</div>');
});

it('cache work properly with tags - rememberForever', function () {
    \Illuminate\Support\Facades\Cache::flush();

    /** @var DishesSetUpTable|\Livewire\Testing\TestableLivewire $component */
    $component = livewire(DishesSetUpTable::class, ['testCache' => [
        Cache::make()
            ->prefix('test-')
            ->forever(),
    ]]);

    expect($component->setUp['cache'])
        ->name->toBe('cache')
        ->prefix->toBe('test-')
        ->enabled->toBe(true)
        ->forever->toBe(true)
        ->ttl->toBe(300);

    $tag      = 'test-powergrid-dishes-default';
    $cacheKey = '{"page":1}-{"search":""}-{"sortDirection":"asc"}-{"sortField":"id"}-{"filters":[]}';

    $tags = \Illuminate\Support\Facades\Cache::tags($tag);

    expect($tags->getTags()->getNames()[0])->toBe($tag);

    /** @var LengthAwarePaginator $items */
    $items = $tags->get($cacheKey);

    expect($items->total())->toBe(15);

    $storage = Livewire\invade($tags->getStore())->storage;

    expect(json_encode($storage))
        ->toContain('{\"page\":1}-{\"search\":\"\"}-{\"sortDirection\":\"asc\"}-{\"sortField\":\"id\"}-{\"filters\":[]}');

    $component->set('search', 'New search');
    $cacheKeySearch = '{"page":1}-{"search":"New search"}-{"sortDirection":"asc"}-{"sortField":"id"}-{"filters":[]}';

    /** @var LengthAwarePaginator $items */
    $items = $tags->get($cacheKeySearch);

    expect($items->total())->toBe(0);

    \Illuminate\Support\Facades\Cache::tags($tag)->flush();

    expect(\Illuminate\Support\Facades\Cache::tags($tag)->get($cacheKey))
        ->toBeNull();
});

it('cache work properly with tags - remember', function () {
    \Illuminate\Support\Facades\Cache::flush();

    /** @var DishesSetUpTable|\Livewire\Testing\TestableLivewire $component */
    $component = livewire(DishesSetUpTable::class, ['testCache' => [
        Cache::make()
            ->ttl(360),
    ]]);

    expect($component->setUp['cache'])
        ->name->toBe('cache')
        ->enabled->toBe(true)
        ->forever->toBe(false)
        ->ttl->toBe(360);

    $tag      = 'powergrid-dishes-default';
    $cacheKey = '{"page":1}-{"search":""}-{"sortDirection":"asc"}-{"sortField":"id"}-{"filters":[]}';

    $tags = \Illuminate\Support\Facades\Cache::tags($tag);

    expect($tags->getTags()->getNames()[0])->toBe($tag);

    /** @var LengthAwarePaginator $items */
    $items = $tags->get($cacheKey);

    expect($items->total())->toBe(15);

    $storage = Livewire\invade($tags->getStore())->storage;

    expect(json_encode($storage))
        ->toContain('{\"page\":1}-{\"search\":\"\"}-{\"sortDirection\":\"asc\"}-{\"sortField\":\"id\"}-{\"filters\":[]}');

    $component->set('search', 'New search');
    $cacheKeySearch = '{"page":1}-{"search":"New search"}-{"sortDirection":"asc"}-{"sortField":"id"}-{"filters":[]}';

    /** @var LengthAwarePaginator $items */
    $items = $tags->get($cacheKeySearch);

    expect($items->total())->toBe(0);

    \Illuminate\Support\Facades\Cache::tags($tag)->flush();

    expect(\Illuminate\Support\Facades\Cache::tags($tag)->get($cacheKey))
        ->toBeNull();
});

it('cache work properly with tags - customTag', function () {
    \Illuminate\Support\Facades\Cache::flush();

    /** @var DishesSetUpTable|\Livewire\Testing\TestableLivewire $component */
    $component = livewire(DishesSetUpTable::class, ['testCache' => [
        Cache::make()
            ->customTag('my-custom-tag')
            ->ttl(360),
    ]]);

    expect($component->setUp['cache'])
        ->name->toBe('cache')
        ->enabled->toBe(true)
        ->forever->toBe(false)
        ->ttl->toBe(360);

    $tag      = 'my-custom-tag';
    $cacheKey = '{"page":1}-{"search":""}-{"sortDirection":"asc"}-{"sortField":"id"}-{"filters":[]}';

    $tags = \Illuminate\Support\Facades\Cache::tags($tag);

    expect($tags->getTags()->getNames()[0])->toBe($tag);

    /** @var LengthAwarePaginator $items */
    $items = $tags->get($cacheKey);

    expect($items->total())->toBe(15);

    $storage = Livewire\invade($tags->getStore())->storage;

    expect(json_encode($storage))
        ->toContain('{\"page\":1}-{\"search\":\"\"}-{\"sortDirection\":\"asc\"}-{\"sortField\":\"id\"}-{\"filters\":[]}');

    $component->set('search', 'New search');
    $cacheKeySearch = '{"page":1}-{"search":"New search"}-{"sortDirection":"asc"}-{"sortField":"id"}-{"filters":[]}';

    /** @var LengthAwarePaginator $items */
    $items = $tags->get($cacheKeySearch);

    expect($items->total())->toBe(0);

    \Illuminate\Support\Facades\Cache::tags($tag)->flush();

    expect(\Illuminate\Support\Facades\Cache::tags($tag)->get($cacheKey))
        ->toBeNull();
});
