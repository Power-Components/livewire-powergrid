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
    Table};

class Tailwind extends ThemeBase
{
    public string $name = 'tailwind';

    public static function paginationTheme(): string
    {
        return 'tailwind';
    }

    public function table(): Table
    {
        return Theme::table('min-w-full divide-y divide-gray-300 border-b dark:bg-gray-600 border-gray-400 ')
            ->thead('bg-gray-50 dark:bg-gray-700')
            ->tr('border border-gray-200 dark:border-gray-400')
            ->th('px-2 pr-4 py-3 text-left text-xs font-medium text-gray-500 tracking-wider whitespace-nowrap dark:text-gray-300')
            ->tbody('text-gray-800')
            ->trBody('border border-gray-200 dark:border-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700')
            ->tdBody('px-2 py-1 whitespace-nowrap dark:text-gray-200')
            ->tdBodyTotalColumns('px-2 py-1 whitespace-nowrap dark:text-gray-200 text-sm text-gray-600 text-right space-y-2');
    }

    public function footer(): Footer
    {
        return Theme::footer()
            ->view($this->root() . '.footer')
            ->select('block appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500  dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500');
    }

    public function actions(): Actions
    {
        return Theme::actions()
            ->headerBtn('block w-full bg-gray-50 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-600 dark:border-gray-500 dark:bg-gray-500 2xl:dark:placeholder-gray-300 dark:text-gray-200 dark:text-gray-300')
            ->rowsBtn('focus:outline-none text-sm py-2.5 px-5 rounded border');
    }

    public function cols(): Cols
    {
        return Theme::cols()
            ->div('')
            ->clearFilter('', '');
    }

    public function rows(): Row
    {
        return Theme::row()
            ->span('flex justify-between');
    }

    public function editable(): Editable
    {
        return Theme::editable()
            ->view($this->root() . '.editable')
            ->span('flex justify-between')
            ->input('dark:bg-gray-700 bg-gray-200 text-black-700 border border-gray-200 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500');
    }

    public function clickToCopy(): ClickToCopy
    {
        return Theme::clickToCopy()
            ->span('flex justify-between');
    }

    public function checkbox(): Checkbox
    {
        return Theme::checkbox()
            ->th('px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider')
            ->label('flex items-center space-x-3')
            ->input('h-4 w-4');
    }

    public function filterBoolean(): FilterBoolean
    {
        return Theme::filterBoolean()
            ->input('appearance-none block mt-1 mb-1 bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500', 'max-width: 370px')
            ->divNotInline('pt-2 p-2')
            ->relativeDiv('pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500')
            ->divInline('');
    }

    public function filterDatePicker(): FilterDatePicker
    {
        return Theme::filterDatePicker()
            ->input('flatpickr flatpickr-input block my-1 bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500', 'min-width: 12rem')
            ->divNotInline('pt-2 p-2')
            ->divInline('');
    }

    public function filterMultiSelect(): FilterMultiSelect
    {
        return Theme::filterMultiSelect()
            ->view($this->root() . '.multi-select')
            ->input('appearance-none block mt-1 mb-1 bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500')
            ->divNotInline('pt-2 p-2')
            ->divInline('pr-6');
    }

    public function filterNumber(): FilterNumber
    {
        return Theme::filterNumber()
            ->input('block bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500', 'min-width: 4rem')
            ->divNotInline('pt-2 p-2')
            ->divInline('pr-6');
    }

    public function filterSelect(): FilterSelect
    {
        return Theme::filterSelect()
            ->input('appearance-none block mt-1 mb-1 bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500')
            ->divNotInline('pt-2 ml-2 p-2')
            ->relativeDiv('pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500')
            ->divInline('pr-6');
    }

    public function filterInputText(): FilterInputText
    {
        return Theme::filterInputText()
            ->select('appearance-none block bg-gray-50 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500', 'pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700')
            ->input('w-full block bg-gray-50 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 dark:bg-gray-500 dark:text-gray-200 dark:placeholder-gray-200 dark:border-gray-500')
            ->divNotInline('mt-1')
            ->divInline('pr-6');
    }
}
