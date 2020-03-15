<?php

declare(strict_types=1);

namespace App\Service\Spreadsheet;

interface SpreadsheetSaverInterface
{
    public function save(Spreadsheet $spreadsheet, ?SpreadsheetSaveOptions $spreadsheetSaveOptions = null);
}