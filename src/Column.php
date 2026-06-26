<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;
use Illuminate\Support\Traits\Macroable;
use Livewire\Wireable;

/**
 * Macros
 *
 * @method static withSummary(string $key, string $label, \Closure $using, bool $header = false, bool $footer = true)
 * @method static naturalSort()
 * @method static searchableRaw(string $sql)
 * @method static searchableJson(string $tableName) // sqlite, mysql
 *
 * Deprecated summary helpers — prefer withSummary() with a closure.
 * @method static withSum(string $label, bool $header = false, bool $footer = true) @deprecated since 7.x, use withSummary() instead
 * @method static withCount(string $label, bool $header = false, bool $footer = true) @deprecated since 7.x, use withSummary() instead
 * @method static withAvg(string $label, bool $header = false, bool $footer = true) @deprecated since 7.x, use withSummary() instead
 * @method static withMin(string $label, bool $header = false, bool $footer = true) @deprecated since 7.x, use withSummary() instead
 * @method static withMax(string $label, bool $header = false, bool $footer = true) @deprecated since 7.x, use withSummary() instead
 */
final class Column implements Wireable
{
    use Macroable;

    public string $title = '';

    public string $field = '';

    public string $dataField = '';

    public string $placeholder = '';

    public bool $searchable = false;

    public bool $enableSort = false;

    public bool $hidden = false;

    public bool $forceHidden = false;

    public ?bool $visibleInExport = null;

    public bool $sortable = false;

    public ?Closure $sortCallback = null;

    public bool $index = false;

    /** @var array<string, mixed> */
    public array $properties = [];

    /** @var list<array<string, mixed>> */
    public array $rawQueries = [];

    public bool $isAction = false;

    public bool $fixedOnResponsive = false;

    public bool $template = false;

    public string $contentClassField = '';

    /** @var string|list<string> */
    public string|array $contentClasses = [];

    public string $headerClass = '';

    public string $headerStyle = '';

    public string $bodyClass = '';

    public string $bodyStyle = '';

    /** @var array<string, mixed> */
    public array $pluginData = [];

    /**
     * Custom summary callbacks declared via withSummary(), keyed by summary key.
     * Closures cannot be serialized, so they are stripped in toLivewire() and
     * re-resolved server-side from the fresh columns() definition.
     *
     * @var array<string, Closure>
     */
    public array $summaryCallbacks = [];

    public mixed $filters = null;

    /** @var array<string, mixed> */
    public array $customContent = [];

    /**
     * Adds a new Column
     */
    public static function add(): self
    {
        return new Column();
    }

    /**
     * Make a new Column
     */
    public static function make(string $title, string $field, string $dataField = ''): self
    {
        return (new Column())
            ->title($title)
            ->field($field, $dataField);
    }

    /**
     * Make a new action
     */
    public static function action(string $title): self
    {
        return (new Column())
            ->title($title)
            ->isAction()
            ->visibleInExport(false);
    }

    public function isAction(): Column
    {
        $this->isAction = true;

        return $this;
    }

    /**
     * Adds title
     */
    public function title(string $title): Column
    {
        $this->title = $title;

        return $this;
    }

    public function fixedOnResponsive(): Column
    {
        $this->fixedOnResponsive = true;

        return $this;
    }

    /**
     * Adds index ($loop->index)
     */
    public function index(): Column
    {
        $this->index = true;

        return $this;
    }

    /**
     * Adds placeholder
     */
    public function placeholder(string $placeholder): Column
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Makes the column searchable
     */
    public function searchable(): Column
    {
        $this->searchable = true;

        return $this;
    }

    public function enableSort(): Column
    {
        $this->enableSort = true;

        return $this;
    }

    /**
     * Adds sort to the column header
     */
    public function sortable(): Column
    {
        $this->enableSort();

        $this->sortable = true;

        return $this;
    }

    /**
     * Sets a custom sorting callback for this column.
     * The callback receives the query builder and sort direction.
     */
    public function sortUsing(Closure $callback): Column
    {
        $this->enableSort();

        $this->sortable = true;

        $this->sortCallback = $callback;

        return $this;
    }

    /**
     * Field in the database
     */
    public function field(string $field, string $dataField = ''): Column
    {
        $this->field = $field;

        $this->dataField = filled($dataField) ? $dataField : $field;

        return $this;
    }

    /**
     * Class html tag header table
     */
    public function headerAttribute(string $classAttr = '', string $styleAttr = ''): Column
    {
        $this->headerClass = $classAttr;
        $this->headerStyle = $styleAttr;

        return $this;
    }

    /**
     * Class html tag body table
     */
    public function bodyAttribute(string $classAttr = '', string $styleAttr = ''): Column
    {
        $this->bodyClass = $classAttr;
        $this->bodyStyle = $styleAttr;

        return $this;
    }

    /**
     * Hide the column
     */
    public function hidden(bool $isHidden = true, bool $isForceHidden = true): Column
    {
        $this->hidden = $isHidden;
        $this->forceHidden = $isForceHidden;

        return $this;
    }

    public function visibleInExport(?bool $visible): Column
    {
        $this->visibleInExport = $visible;

        return $this;
    }

    public function contentClassField(string $dataField = ''): Column
    {
        $this->contentClassField = $dataField;

        return $this;
    }

    /** @param  string|list<string>  $contentClasses */
    public function contentClasses(string|array $contentClasses): Column
    {
        $this->contentClasses = $contentClasses;

        return $this;
    }

    public function template(): Column
    {
        $this->template = true;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toLivewire(): array
    {
        $data = (array) $this;

        // Closures cannot be serialized, exclude them
        unset($data['sortCallback']);
        unset($data['rawQueries']);
        unset($data['summaryCallbacks']);

        return $data;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
