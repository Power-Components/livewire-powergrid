## PowerGrid - Livewire DataTable Component

PowerGrid (`power-components/livewire-powergrid`) is a Laravel Livewire component for building dynamic, feature-rich data tables with minimal boilerplate.

### Core Architecture (v7)

PowerGrid 7.x prioritizes **simplicity, an adaptable native skeleton, and independent test components**.

- **PHP:** 8.3+
- **Livewire:** 4.0+
- **Tailwind CSS:** 4.x+
- **Required Package:** `power-components/partials` for DOM isolation

### Artisan Commands

- Create a PowerGrid component: `php artisan powergrid:create MyTable`

### Key Concepts

#### Component Structure

Every PowerGrid table extends `PowerGridComponent` and defines three core methods:

@verbatim
<code-snippet name="PowerGrid Component" lang="php">
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

class UserTable extends PowerGridComponent
{
    public function datasource(): ?Builder
    {
        return User::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('created_at_formatted', fn ($row) => $row->created_at->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable(),
            Column::make('Created', 'created_at_formatted', 'created_at')->sortable(),
        ];
    }
}
</code-snippet>
@endverbatim

#### Theming System

PowerGrid 7.x is section-based. `struct()` only sets `baseView`; each token group is a public method (`layout()`, `header()`, `table()`, `footer()`, `cols()`, `tabs()`, plus `filter()` / `editable()` / `toggleable()`). CSS classes live in tokens read by `theme()` / `theme_view()`:

@verbatim
<code-snippet name="Theme Token Access" lang="php">
// In Blade views
{{ theme('table.layout.td') }}
{{ theme('header.layout.container') }}
{{ theme('tabs.tab_active') }}
{{ theme_view('pagination') }}
{{ theme_view('tabs') }}
</code-snippet>
@endverbatim

Three ways to restyle, cheapest first:

1. **No-code:** `config('livewire-powergrid.theme_overrides')` — a nested token array merged last (highest precedence). No Theme class needed.
2. **Section methods** on a Theme class — plain nested arrays (6.x-familiar) or the fluent `$this->section()` helper. A child overrides only the sections it changes; the rest inherit via `parentTheme`.
3. **`ArrayTheme`** — data-first theme from a plain array (`fromArray()` / `fromFile()`, or a subclass whose `struct()` returns an array).

Available themes: `Tailwind` (root, `parentTheme = null`), `DaisyUI` and `Flux` (both `parentTheme = Tailwind::class`). DaisyUI ships **zero** blades (fully token-driven, inherits Tailwind's markup). Flux keeps `<flux:*>` blades only where the HTML differs. Prefer tokens over new blades.

`tabs` is a theme-aware token group (`list`, `tab`, `tab_active`, `tab_inactive`, `badge`, `badge_active`, `badge_inactive`, optional `view`). The view resolves via `theme_view('tabs')`.

Theme configuration in `config/livewire-powergrid.php` accepts a registered **name** or an FQCN. Register a custom theme with `PowerGridManager::registerTheme('bootstrap', BootstrapTheme::class)`.

@verbatim
<code-snippet name="Theme Configuration" lang="php">
'theme' => 'tailwind', // 'daisyui' | 'flux' | FQCN also work

'theme_overrides' => [
    'table' => ['layout' => ['th' => 'font-bold px-4 py-3']],
    'tabs'  => ['tab_active' => 'bg-emerald-100 text-emerald-800'],
],
</code-snippet>
@endverbatim

#### Filters

@verbatim
<code-snippet name="Adding Filters" lang="php">
use PowerComponents\LivewirePowerGrid\Facades\Filter;

public function filters(): array
{
    return [
        Filter::inputText('name')->operators(['contains', 'starts_with']),
        Filter::select('status')->dataSource(collect(['active', 'inactive'])),
        Filter::boolean('is_admin')->label('Admin', 'User'),
        Filter::number('age'),
        Filter::datePicker('created_at'),
    ];
}
</code-snippet>
@endverbatim

#### Actions (Server-Side Rendered)

Actions are fully rendered server-side as Blade components via `renderActions()`. No JavaScript processing or caching.

@verbatim
<code-snippet name="Table Actions" lang="php">
use PowerComponents\LivewirePowerGrid\Button;

public function actions($row): array
{
    return [
        Button::make('edit')
            ->slot('Edit')
            ->class('btn btn-primary')
            ->route('users.edit', ['user' => $row->id]),

        Button::make('delete')
            ->slot('Delete')
            ->class('btn btn-danger')
            ->dispatch('deleteUser', ['id' => $row->id]),
    ];
}
</code-snippet>
@endverbatim

#### Performance & Partials

PowerGrid uses `power-components/partials` fragments to isolate DOM updates via four Hot Zones:
1. `pg-tbody` - Table body updates
2. `pg-pagination` - Pagination updates
3. `pg-filter-fields` / `pg-enabled-filters` - Filter pills and panel fields
4. `pg-tabs` - Status tabs

#### Per-Component Theme Override

@verbatim
<code-snippet name="Custom Theme Per Component" lang="php">
// Swap entire theme class
public function customThemeClass(): ?string
{
    return \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class;
}

// Or merge specific tokens
public function template(): ?Theme
{
    return Tailwind::make()->merge([
        'table' => [
            'layout' => [
                'tr' => 'hover:bg-gray-50 dark:hover:bg-gray-800',
            ],
        ],
    ]);
}
</code-snippet>
@endverbatim

### Views Architecture

- **No Theme Conditionals:** Never use `@if($theme == 'bootstrap')` in Blade files.
- **Token-driven views:** A theme ships a Blade file only when the HTML genuinely differs. DaisyUI ships zero blades and inherits Tailwind's markup via `parentTheme`.
- **Micro-files Strategy:** Table views are divided into single-purpose micro-files (`index.blade.php`, `tbody.blade.php`, `td.blade.php`).
- **Directives:** Use `@theme()` directives exclusively.

### Testing Best Practices

- Tests must be self-sufficient and theme-independent.
- Focus on core engine capabilities: Search, Filters, Sort, and Pagination.
- Build mini-components at runtime within test files (no global fixtures).

@verbatim
<code-snippet name="PowerGrid Test Pattern" lang="php">
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

test('can search by name', function () {
    $component = new class extends PowerGridComponent {
        public function datasource(): ?Builder
        {
            return User::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name')->searchable(),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});
</code-snippet>
@endverbatim

### Plugins

PowerGrid supports external plugins for custom column behaviors (e.g., selectable, colorpicker, rating). Plugins are created in the user's project at `app/PowerGrid/Plugins/{PluginName}/` and extend `PluginBase`.

@verbatim
<code-snippet name="Plugin Column Macro" lang="php">
// After registering a plugin, use it in columns:
Column::make('Status', 'status')->selectable(['active', 'inactive', 'pending']);
</code-snippet>
@endverbatim

### Configuration

Key configuration options in `config/livewire-powergrid.php`:

- `theme` - Registered name (`tailwind` / `daisyui` / `flux`) or FQCN
- `theme_overrides` - Nested token overrides (highest precedence, no Theme class)
- `plugins` - Registered plugin classes
- `pagination` - Default pagination type and per-page options
