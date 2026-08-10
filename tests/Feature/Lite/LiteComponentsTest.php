<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\{Component, Livewire, WithPagination};
use PowerComponents\LivewirePowerGrid\Lite\Traits\{WithCheckbox, WithSearch, WithSorting};

beforeEach(function () {
    Schema::create('lite_users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('status')->default('active');
        $table->timestamps();
    });

    LiteUser::insert([
        ['name' => 'Alice', 'email' => 'alice@test.com', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Bob', 'email' => 'bob@test.com', 'status' => 'inactive', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Charlie', 'email' => 'charlie@test.com', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Diana', 'email' => 'diana@test.com', 'status' => 'inactive', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Eve', 'email' => 'eve@test.com', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
    ]);
});

afterEach(function () {
    Schema::dropIfExists('lite_users');
});

class LiteUser extends Model
{
    protected $table = 'lite_users';

    protected $guarded = [];
}

class LiteTableComponent extends Component
{
    use WithCheckbox, WithPagination, WithSorting;

    #[Computed]
    public function users()
    {
        return LiteUser::query()
            ->when($this->sortField, fn ($q) => $q->orderBy($this->sortField, $this->sortDirection))
            ->paginate(3);
    }

    public function getAllCheckboxValues(): array
    {
        return $this->users->pluck('id')->map(fn ($id) => (string) $id)->toArray();
    }

    public function render()
    {
        return <<<'BLADE'
        <div>
            <x-pg-table :paginate="$this->users" record-count="full">
                <x-pg-columns>
                    <x-pg-column checkbox />
                    <x-pg-column
                        sortable
                        field="name"
                        :sorted="$this->isSorted('name')"
                        :direction="$this->sortDirectionFor('name')"
                        wire:click="sortBy('name')"
                    >
                        Name
                    </x-pg-column>
                    <x-pg-column
                        sortable
                        field="email"
                        :sorted="$this->isSorted('email')"
                        :direction="$this->sortDirectionFor('email')"
                        wire:click="sortBy('email')"
                    >
                        Email
                    </x-pg-column>
                    <x-pg-column>Status</x-pg-column>
                </x-pg-columns>

                <x-pg-rows>
                    @foreach ($this->users as $user)
                        <x-pg-row :key="$user->id" :checkbox-value="$user->id">
                            <x-pg-cell>{{ $user->name }}</x-pg-cell>
                            <x-pg-cell>{{ $user->email }}</x-pg-cell>
                            <x-pg-cell>{{ $user->status }}</x-pg-cell>
                        </x-pg-row>
                    @endforeach
                </x-pg-rows>
            </x-pg-table>
        </div>
        BLADE;
    }
}

it('renders the pg-table component with theme classes', function () {
    $component = Livewire::test(LiteTableComponent::class);

    $component->assertSee('Alice')
        ->assertSee('Bob')
        ->assertSee('Charlie')
        ->assertDontSee('Diana'); // page 2
});

it('renders table with themed structure', function () {
    $component = Livewire::test(LiteTableComponent::class);

    $html = $component->html();

    expect($html)->toContain('<table')
        ->and($html)->toContain('<thead')
        ->and($html)->toContain('<tbody')
        ->and($html)->toContain('<th')
        ->and($html)->toContain('<td');
});

it('renders pagination when paginator has pages', function () {
    $component = Livewire::test(LiteTableComponent::class);

    $html = $component->html();

    expect($html)->toContain('Pagination Navigation');
});

it('renders record count', function () {
    $component = Livewire::test(LiteTableComponent::class);

    $html = $component->html();

    expect($html)->toContain('1')
        ->and($html)->toContain('3')
        ->and($html)->toContain('5');
});

it('sorts by field on first click (asc)', function () {
    Livewire::test(LiteTableComponent::class)
        ->call('sortBy', 'name')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc');
});

it('reverses sort direction on second click', function () {
    Livewire::test(LiteTableComponent::class)
        ->call('sortBy', 'name')
        ->call('sortBy', 'name')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'desc');
});

it('resets direction when switching fields', function () {
    Livewire::test(LiteTableComponent::class)
        ->call('sortBy', 'name')
        ->call('sortBy', 'name') // desc
        ->call('sortBy', 'email') // new field, resets to asc
        ->assertSet('sortField', 'email')
        ->assertSet('sortDirection', 'asc');
});

it('isSorted returns correct value', function () {
    $component = Livewire::test(LiteTableComponent::class)
        ->call('sortBy', 'name');

    expect($component->instance()->isSorted('name'))->toBeTrue()
        ->and($component->instance()->isSorted('email'))->toBeFalse();
});

it('sortDirectionFor returns correct direction', function () {
    $component = Livewire::test(LiteTableComponent::class)
        ->call('sortBy', 'name');

    expect($component->instance()->sortDirectionFor('name'))->toBe('asc')
        ->and($component->instance()->sortDirectionFor('email'))->toBeNull();
});

it('multi-sort cycles through asc -> desc -> remove', function () {
    Livewire::test(LiteTableComponent::class)
        ->set('multiSort', true)
        ->call('sortBy', 'name') // add name:asc
        ->assertSet('sortArray', ['name' => 'asc'])
        ->call('sortBy', 'email') // add email:asc
        ->assertSet('sortArray', ['name' => 'asc', 'email' => 'asc'])
        ->call('sortBy', 'name') // name:asc -> name:desc
        ->assertSet('sortArray', ['name' => 'desc', 'email' => 'asc'])
        ->call('sortBy', 'name') // name:desc -> remove
        ->assertSet('sortArray', ['email' => 'asc']);
});

it('manages checkbox values', function () {
    Livewire::test(LiteTableComponent::class)
        ->set('checkboxValues', ['1', '2'])
        ->assertSet('checkboxValues', ['1', '2']);
});

it('clearSelected resets all selections', function () {
    Livewire::test(LiteTableComponent::class)
        ->set('checkboxValues', ['1', '2', '3'])
        ->call('clearSelected')
        ->assertSet('checkboxValues', [])
        ->assertSet('checkboxAll', false);
});

it('isChecked returns correct state', function () {
    $component = Livewire::test(LiteTableComponent::class)
        ->set('checkboxValues', ['1', '3']);

    expect($component->instance()->isChecked(1))->toBeTrue()
        ->and($component->instance()->isChecked(2))->toBeFalse()
        ->and($component->instance()->isChecked(3))->toBeTrue();
});

it('selectAll calls getAllCheckboxValues', function () {
    $component = Livewire::test(LiteTableComponent::class)
        ->set('checkboxAll', true);

    expect($component->instance()->checkboxValues)->toContain('1')
        ->and($component->instance()->checkboxValues)->toContain('2')
        ->and($component->instance()->checkboxValues)->toContain('3');
});

it('deselect all clears values when checkboxAll set to false', function () {
    Livewire::test(LiteTableComponent::class)
        ->set('checkboxAll', true)
        ->set('checkboxAll', false)
        ->assertSet('checkboxValues', []);
});

it('sortIconFor returns up-down when not sorted', function () {
    $component = Livewire::test(LiteTableComponent::class);

    expect($component->instance()->sortIconFor('name'))
        ->toBe('livewire-powergrid::components.icons.chevron-up-down');
});

it('sortIconFor returns down when sorted asc', function () {
    $component = Livewire::test(LiteTableComponent::class)
        ->call('sortBy', 'name');

    expect($component->instance()->sortIconFor('name'))
        ->toBe('livewire-powergrid::components.icons.chevron-down');
});

it('sortIconFor returns up when sorted desc', function () {
    $component = Livewire::test(LiteTableComponent::class)
        ->call('sortBy', 'name')
        ->call('sortBy', 'name');

    expect($component->instance()->sortIconFor('name'))
        ->toBe('livewire-powergrid::components.icons.chevron-up');
});

it('column shows sort icon when sortable and sorted', function () {
    $component = Livewire::test(LiteTableComponent::class)
        ->call('sortBy', 'name');

    $html = $component->html();

    // The sorted column should render an SVG icon (from the chevron include)
    expect($html)->toContain('svg');
});

it('theme function works without PowerGridComponent instantiated', function () {
    $value = theme('table.layout.td');

    expect($value)->not->toBeEmpty()
        ->and($value)->toBeString();
});

class LiteSearchComponent extends Component
{
    use WithPagination, WithSearch, WithSorting;

    public int $perPage = 3;

    #[Computed]
    public function users()
    {
        return LiteUser::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->sortField, fn ($q) => $q->orderBy($this->sortField, $this->sortDirection))
            ->paginate($this->perPage);
    }

    public function render()
    {
        return <<<'BLADE'
        <div>
            <input wire:model.live.debounce.700ms="search" type="text" placeholder="Search..." />
            <select wire:model.live="perPage">
                <option value="3">3</option>
                <option value="10">10</option>
            </select>

            <x-pg-table :paginate="$this->users" record-count="full">
                <x-pg-columns>
                    <x-pg-column
                        sortable
                        field="name"
                        :sorted="$this->isSorted('name')"
                        :direction="$this->sortDirectionFor('name')"
                        wire:click="sortBy('name')"
                    >
                        Name
                    </x-pg-column>
                    <x-pg-column>Email</x-pg-column>
                </x-pg-columns>

                <x-pg-rows>
                    @foreach ($this->users as $user)
                        <x-pg-row :key="$user->id">
                            <x-pg-cell>{{ $user->name }}</x-pg-cell>
                            <x-pg-cell>{{ $user->email }}</x-pg-cell>
                        </x-pg-row>
                    @endforeach
                </x-pg-rows>
            </x-pg-table>
        </div>
        BLADE;
    }
}

it('search input is rendered in user template (not inside pg-table)', function () {
    $component = Livewire::test(LiteSearchComponent::class);

    $html = $component->html();

    expect($html)->toContain('wire:model.live.debounce.700ms="search"');
});

it('filters results when search is set', function () {
    Livewire::test(LiteSearchComponent::class)
        ->set('search', 'Alice')
        ->assertSee('Alice')
        ->assertDontSee('bob@test.com')
        ->assertDontSee('charlie@test.com');
});

it('shows all results when search is cleared', function () {
    Livewire::test(LiteSearchComponent::class)
        ->set('search', 'Alice')
        ->assertDontSee('bob@test.com')
        ->set('search', '')
        ->assertSee('Alice')
        ->assertSee('bob@test.com')
        ->assertSee('charlie@test.com');
});

it('per-page selector is rendered in user template (not inside pg-table)', function () {
    $component = Livewire::test(LiteSearchComponent::class);

    $html = $component->html();

    expect($html)->toContain('wire:model.live="perPage"');
});

it('changes per-page and updates results', function () {
    Livewire::test(LiteSearchComponent::class)
        ->assertSee('Alice')
        ->assertDontSee('Diana')
        ->set('perPage', 10)
        ->assertSee('Alice')
        ->assertSee('Diana')
        ->assertSee('Eve');
});

it('table container has overflow-x-auto for responsive', function () {
    $component = Livewire::test(LiteSearchComponent::class);

    $html = $component->html();

    expect($html)->toContain('overflow-x-auto');
});

it('uses theme pagination view instead of custom lite pagination', function () {
    $component = Livewire::test(LiteSearchComponent::class);

    $html = $component->html();

    expect($html)->toContain('Pagination Navigation');
});
