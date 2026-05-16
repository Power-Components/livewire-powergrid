<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

use Closure;

class Table
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    public function viewLayout(string $viewLayout): self
    {
        $this->properties['viewLayout'] = $viewLayout;

        return $this;
    }

    // Alias 'table.header' → arquivo 'table.tr' (nome divergente, precisa ser declarado)
    public function viewHeader(string $viewHeader): self
    {
        $this->properties['viewHeader'] = $viewHeader;

        return $this;
    }

    // Alias 'table.row' → arquivo 'table.row'
    public function viewRow(string $viewRow): self
    {
        $this->properties['viewRow'] = $viewRow;

        return $this;
    }

    // Alias 'table.cols' → arquivo 'table.cols'
    public function viewCols(string $viewCols): self
    {
        $this->properties['viewCols'] = $viewCols;

        return $this;
    }

    // Alias 'table.th-empty' → arquivo 'table.th-empty'
    public function viewThEmpty(string $viewThEmpty): self
    {
        $this->properties['viewThEmpty'] = $viewThEmpty;

        return $this;
    }

    // Alias 'table.inline-filters' → arquivo 'table.inline-filters'
    public function viewInlineFilters(string $viewInlineFilters): self
    {
        $this->properties['viewInlineFilters'] = $viewInlineFilters;

        return $this;
    }

    // Alias 'table.checkbox-all' → arquivo 'table.checkbox-all'
    public function viewCheckboxAll(string $viewCheckboxAll): self
    {
        $this->properties['viewCheckboxAll'] = $viewCheckboxAll;

        return $this;
    }

    // Alias 'table.checkbox-row' → arquivo 'table.checkbox-row'
    public function viewCheckboxRow(string $viewCheckboxRow): self
    {
        $this->properties['viewCheckboxRow'] = $viewCheckboxRow;

        return $this;
    }

    // Alias 'table.radio-row' → arquivo 'table.radio-row'
    public function viewRadioRow(string $viewRadioRow): self
    {
        $this->properties['viewRadioRow'] = $viewRadioRow;

        return $this;
    }

    public function layout(Closure|array $callback): self
    {
        $component = new Layout();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['layout'] = $component->toArray();

        return $this;
    }

    public function body(Closure|array $callback): self
    {
        $component = new Body();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['body'] = $component->toArray();

        return $this;
    }

    public function checkbox(Closure|array $callback): self
    {
        $component = new Checkbox();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['checkbox'] = $component->toArray();

        return $this;
    }

    public function radio(Closure|array $callback): self
    {
        $component = new Radio();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['radio'] = $component->toArray();

        return $this;
    }
}
