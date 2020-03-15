<?php

declare(strict_types=1);

namespace App\Service\Spreadsheet;

final class Spreadsheet implements SpreadsheetInterface
{
    /** @var string */
    private $fileName;
    /** @var array */
    private $data;

    public function __construct(string $fileName, array $data)
    {
        $this->fileName = $fileName;
        $this->data = $data;
    }

    public function getFilename(): string
    {
        return $this->fileName;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
