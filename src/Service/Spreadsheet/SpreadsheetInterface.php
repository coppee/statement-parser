<?php

declare(strict_types=1);

namespace App\Service\Spreadsheet;

interface SpreadsheetInterface
{
    public function getFilename(): string;
    public function getData(): array;
}
