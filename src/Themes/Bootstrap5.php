<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components\{Actions,
    Checkbox,
    ClickToCopy,
    Cols,
    Editable,
    FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect,
    Footer,
    Row,
    Table,
    Toggleable};

class Bootstrap5 extends ThemeBase
{
    public string $name = 'bootstrap5';

    public static function paginationTheme(): string
    {
        return 'bootstrap';
    }

    public function table(): Table
    {
        return Theme::table('table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2')
            ->thead('')
            ->tr('')
            ->th('', 'white-space: nowrap;min-width: 50px;padding-left: 15px;font-size: 0.75rem;color: #6b6a6a;padding-top: 8px;padding-bottom: 8px;')
            ->tbody('')
            ->tdBody('', 'vertical-align: middle; line-height: normal;')
            ->tdBodyTotalColumns('', 'font-size: 0.875rem; line-height: 1.25rem; --tw-text-opacity: 1; color: rgb(76 79 82 / var(--tw-text-opacity)); padding-left: 0.75rem; padding-right: 0.75rem; padding-top: 0.5rem; padding-bottom: 0.5rem;')
            ->tdBody('', 'vertical-align: middle; line-height: normal;white-space: nowrap;');
    }

    public function cols(): Cols
    {
        return Theme::cols()
            ->div('')
            ->clearFilter('', 'color: #c30707; cursor:pointer; float: right;');
    }

    public function rows(): Row
    {
        return Theme::row()
            ->span('d-flex justify-content-between');
    }

    public function footer(): Footer
    {
        return Theme::footer()
            ->view($this->root() . '.footer')
            ->select('');
    }

    public function actions(): Actions
    {
        return Theme::actions()
            ->tdBody('text-center')
            ->rowsBtn('');
    }

    public static function styles(): string
    {
        return 'bootstrap';
    }

    public function toggleable(): Toggleable
    {
        return Theme::toggleable()
            ->view($this->root() . '.toggleable');
    }

    public function editable(): Editable
    {
        return Theme::editable()
            ->view($this->root() . '.editable')
            ->span('d-flex justify-content-between')
            ->button('width: 100%;text-align: left;border: 0;padding: 4px;background: none')
            ->input('form-control shadow-none');
    }

    public function clickToCopy(): ClickToCopy
    {
        return Theme::clickToCopy()
            ->span('d-flex justify-content-between');
    }

    public function checkbox(): Checkbox
    {
        return Theme::checkbox()
            ->th('', 'font-size: 1rem !important;text-align:center')
            ->div('form-check')
            ->label('form-check-label')
            ->input('form-check-input shadow-none');
    }

    public function filterBoolean(): FilterBoolean
    {
        return Theme::filterBoolean()
            ->input('form-control shadow-none')
            ->relativeDiv('d-none')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterDatePicker(): FilterDatePicker
    {
        return Theme::filterDatePicker()
            ->input('form-control shadow-none')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterMultiSelect(): FilterMultiSelect
    {
        return Theme::filterMultiSelect()
            ->view($this->root() . '.multi-select')
            ->input('shadow-none')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterNumber(): FilterNumber
    {
        return Theme::filterNumber()
            ->input('form-control shadow-none')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterSelect(): FilterSelect
    {
        return Theme::filterSelect()
            ->input('form-control shadow-none')
            ->relativeDiv('d-none')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterInputText(): FilterInputText
    {
        return Theme::filterInputText()
            ->select('form-control mb-1 shadow-none', 'd-none')
            ->input('form-control shadow-none')
            ->divNotInline('')
            ->divInline('');
    }
}
