<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Export\Contracts;

use PowerComponents\Turbine\Components\SetUp\Exportable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface ExportInterface
{
    /** @param  array<string, mixed>  $exportOptions */
    public function download(array $exportOptions): BinaryFileResponse;

    /** @param  array<string, mixed>  $exportOptions */
    public function store(array $exportOptions): void;

    /** @param  Exportable|array<string, mixed>  $exportOptions */
    public function build(Exportable|array $exportOptions): void;
}
