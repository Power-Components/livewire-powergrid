<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Traits\Macroable;
use Livewire\Wireable;

/**
 * Macros
 * @method static naturalSort()
 * @method static searchableRaw(string $sql)
 * @method static searchableJson(string $tableName) // sqlite, mysql
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

    public bool $index = false;

    public array $properties = [];

    public array $rawQueries = [];

    public bool $isAction = false;

    public bool $fixedOnResponsive = false;

    public bool $template = false;

    public string $contentClassField = '';

    public string|array $contentClasses = [];

    public string $headerClass = '';

    public string $headerStyle = '';

    public string $bodyClass = '';

    public string $bodyStyle = '';

    public array $toggleable = [];

    public array $editable = [];

    public mixed $filters = null;

    public array $customContent = [];

    /**
     * Adds a new Column
     *
     * @return self
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
     *
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
     *
     */
    public function index(): Column
    {
        $this->index = true;

        return $this;
    }

    /**
     * Adds placeholder
     *
     */
    public function placeholder(string $placeholder): Column
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Makes the column searchable
     *
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
     *
     * @return Column
     */
    public function sortable(): Column
    {
        $this->enableSort();

        $this->sortable = true;

        return $this;
    }

    /**
     * Field in the database
     *
     */
    public function field(string $field, string $dataField = ''): Column
    {
        $this->field = $field;

        $this->dataField = filled($dataField) ? $dataField : $field;

        return $this;
    }

    /**
    * Class html tag header table
     *
     */
    public function headerAttribute(string $classAttr = '', string $styleAttr = ''): Column
    {
        $this->headerClass = $classAttr;
        $this->headerStyle = $styleAttr;

        return $this;
    }

    /**
    * Class html tag body table
     *
     */
    public function bodyAttribute(string $classAttr = '', string $styleAttr = ''): Column
    {
        $this->bodyClass = $classAttr;
        $this->bodyStyle = $styleAttr;

        return $this;
    }

    /**
    * Hide the column
     *
     */
    public function hidden(bool $isHidden = true, bool $isForceHidden = true): Column
    {
        $this->hidden      = $isHidden;
        $this->forceHidden = $isForceHidden;

        return $this;
    }

    public function visibleInExport(?bool $visible): Column
    {
        $this->visibleInExport = $visible;

        return $this;
    }

    /**
     * Adds Edit on click to a column
     *
     */
    public function editOnClick(
        bool $hasPermission = true,
        string $dataField = '',
        ?string $fallback = null,
        bool $saveOnMouseOut = false
    ): Column {
        $this->editable = [
            'hasPermission'  => $hasPermission,
            'fallback'       => $fallback,
            'saveOnMouseOut' => $saveOnMouseOut,
        ];

        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * Adds Toggle to a column
     *
     */
    public function toggleable(
        bool $hasPermission = true,
        string $trueLabel = 'Yes',
        string $falseLabel = 'No',
    ): Column {
        $this->editable   = [];
        $this->toggleable = [
            'enabled' => $hasPermission,
            'default' => [$trueLabel,  $falseLabel],
        ];

        return $this;
    }

    public function contentClassField(string $dataField = ''): Column
    {
        $this->contentClassField = $dataField;

        return $this;
    }

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

    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
