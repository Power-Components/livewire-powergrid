# Filter Builder

The **Filter Builder** is a Flux-only plugin that adds a modal where users compose
advanced, multi-condition filters (`AND`/`OR`) on top of your table — without you
writing any query code. Conditions are built from your existing `filters()`
definitions, validated against that allowlist, and applied to both database
queries and collections.

> **Requires the Flux theme.** On any other theme the plugin renders nothing.

---

## Setup

Opt in from your component's `setUp()` method:

```php
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

public function setUp(): array
{
    return [
        PowerGrid::filterBuilder(),
    ];
}
```

The columns, types and operators shown in the modal come **entirely** from your
`filters()` definitions — you never redeclare them here.

```php
public function filters(): array
{
    return [
        Filter::inputText('name'),
        Filter::number('price'),
        Filter::datepicker('created_at'),
        Filter::boolean('active'),
    ];
}
```

### Configuration

All methods are chainable on `PowerGrid::filterBuilder()`:

| Method | Description | Default |
|--------|-------------|---------|
| `match(string $match)` | Default connector when the modal opens (`'and'` or `'or'`). | `'and'` |
| `maxConditions(int $max)` | Hard cap on how many conditions may be applied (anti-abuse). | `30` |
| `hideDefaultFilters(bool $hide = true)` | Hide the inline/outside filters so the builder is the only filtering UI. The enabled-filter pills stay visible. | `false` |
| `only(array $fields)` | Restrict which `filters()` columns appear in the builder (empty = all). | `[]` |
| `except(array $fields)` | Hide specific `filters()` columns from the builder. | `[]` |
| `persist(bool $persist = true)` | Persist applied conditions across requests. See [Persistence](#persistence). | `false` |

```php
public function setUp(): array
{
    return [
        PowerGrid::filterBuilder()
            ->match('or')
            ->maxConditions(10)
            ->only(['name', 'price', 'created_at'])
            ->hideDefaultFilters(),
    ];
}
```

---

## Before query hook

Override `beforeFilterBuilderApply()` on your component to run **right before** the
builder applies its conditions to the datasource. It receives the query builder
(database) or the collection, plus the already validated/normalized
conditions (`{match, rows}`).

Use it to **track/log** what is being filtered, or to **add your own constraints**
to the query. Return the (optionally modified) query/collection to feed it back
into the builder.

```php
public function beforeFilterBuilderApply(mixed $query, array $conditions): mixed
{
    // Track what the user is filtering on.
    logger()->info('Filter Builder applied', $conditions);

    // Optionally narrow the query with your own rules.
    return $query->where('tenant_id', auth()->user()->tenant_id);
}
```

The `$conditions` array shape:

```php
[
    'match' => 'and',        // 'and' | 'or'
    'rows'  => [
        ['column' => 'name', 'operator' => 'contains', 'value' => 'a', 'value2' => '', 'boolean' => 'and'],
        // ...
    ],
]
```

> The hook fires only when there is at least one applied condition, and works for
> both the Eloquent/Query Builder and Collection datasources.

### Validating before "Apply"

Override `validateFilterBuilder()` to validate the conditions the moment the user
clicks **Apply**, before they are committed. The conditions have already been
validated/normalized against your `filters()` allowlist. **Throw** to reject the
submission — nothing is applied and the previous state is kept.

```php
public function validateFilterBuilder(array $conditions): void
{
    foreach ($conditions['rows'] as $row) {
        if ($row['column'] === 'price' && (float) $row['value'] < 0) {
            throw new \InvalidArgumentException('Price cannot be negative.');
        }
    }
}
```

---

## Persistence

The Filter Builder plugs into PowerGrid's persistence layer, so applied conditions
survive page reloads using the configured driver (`cookies`, `session` or `cache`).

There are two ways to enable it:

**1. Together with the regular filters** — if your component already persists
filters, the builder is included automatically:

```php
public function setUp(): array
{
    $this->persist(['filters']);

    return [PowerGrid::filterBuilder()];
}
```

**2. Builder only** — enable persistence just for the builder, without opting the
whole component into filter persistence:

```php
public function setUp(): array
{
    return [
        PowerGrid::filterBuilder()->persist(),
    ];
}
```

In both cases the builder reuses the same storage driver and key as
`persist(['filters'])`. When only the builder is persisted, its enabled-filter
pills are rebuilt from the restored conditions so they survive the reload too.

Configure the driver in `config/livewire-powergrid.php`:

```php
'persist_driver' => 'cookies', // 'cookies' | 'session' | 'cache'
'persist_driver_store' => null, // cache store name when using the 'cache' driver
```
