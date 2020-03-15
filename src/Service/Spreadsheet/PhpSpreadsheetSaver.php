<?php

declare(strict_types=1);

namespace App\Service\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet as PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class PhpSpreadsheetSaver implements SpreadsheetSaverInterface
{
    public function save(Spreadsheet $spreadsheet, ?SpreadsheetSaveOptions $options = null)
    {
        $PhpSpreadsheet = new PhpSpreadsheet();
        Cell::setValueBinder( new AdvancedValueBinder() );

        $sheet = $PhpSpreadsheet->getActiveSheet();
        $sheet->getStyle('A1:J1')->applyFromArray($options->getColumnStyles());

        $columnLetter = 'A';
        foreach ($options->getColumnNames() as $columnName) {
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            $sheet->setCellValue($columnLetter.'1', "\n$columnName\n");
            $columnLetter++;
        }

        $i=2;

        foreach ($spreadsheet->getData() as $operation) {
            $columnLetter = 'A';
            foreach ($operation as $column) {
                $sheet->setCellValue($columnLetter.$i, $column);
                $columnLetter++;
            }
            $i++;
        }

        $xlsxFile = new Xlsx($PhpSpreadsheet);
        $filePath = $options->getDirectoryPath() . '/' . $spreadsheet->getFilename();
        try {
            $xlsxFile->save($filePath);
        } catch (WriterException $exception) {
            dd(\sprintf('Error creating file at %s', $exception->getPath()));
        }
    }
}
