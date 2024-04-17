<?php

namespace PowerComponents\LivewirePowerGrid;

use Livewire\Wireable;

final class Footer implements Wireable
{
    public string $name = 'footer';

    public int $perPage;

    public array $perPageValues = [];

    public string $recordCount = '';

    public ?string $pagination = null;

    public string $includeViewOnTop = '';

    public string $includeViewOnBottom = '';

    public string $pageName = 'page';

    public static function make(): self
    {
        return new Footer();
    }

    public function showPerPage(int $perPage = 10, array $perPageValues = [10, 25, 50, 100, 0]): Footer
    {
        $this->perPage       = $perPage;
        $this->perPageValues = $perPageValues;

        return $this;
    }

    /**
     * default full. other: short, min
     */
    public function showRecordCount(string $mode = 'full'): Footer
    {
        $this->recordCount = $mode;

        return $this;
    }

    /**
     * Custom pagination
     */
    public function pagination(string $viewPath): Footer
    {
        $this->pagination = $viewPath;

        return $this;
    }

    /**
     * Include custom view on top
     */
    public function includeViewOnTop(string $viewPath): Footer
    {
        $this->includeViewOnTop = $viewPath;

        return $this;
    }

    /**
     * Include custom view on bottom
     */
    public function includeViewOnBottom(string $viewPath): Footer
    {
        $this->includeViewOnBottom = $viewPath;

        return $this;
    }

    public function pageName(string $pageName = 'page'): Footer
    {
        $pageName = (string) preg_replace('/[^a-z0-9]/i', '', $pageName);

        if (trim($pageName) === '') {
            $pageName = 'page';
        }

        $this->pageName = $pageName;

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
