<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports\Contracts;

use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
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
