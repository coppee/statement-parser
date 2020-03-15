<?php

declare(strict_types=1);

namespace App\Service\Spreadsheet;

final class SpreadsheetSaveOptions
{
    /** @var array */
    private $columnNames;
    /** @var array*/
    private $columnStyles;
    /** @var string*/
    private $directoryPath;

    private const COLUMN_NAMES = 'columnNames';
    private const COLUMN_STYLES = 'columnStyles';
    private const DIRECTORY_PATH = 'directoryPath';

    public function __construct(array $options)
    {
        $this->setColumnNames($options[self::COLUMN_NAMES]);
        $this->setColumnStyles($options[self::COLUMN_STYLES]);
        $this->setDirectoryPath($options[self::DIRECTORY_PATH]);
    }

    public function getColumnNames(): array
    {
        return $this->columnNames;
    }

    public function getColumnStyles(): array
    {
        return $this->columnStyles;
    }

    public function getDirectoryPath(): string
    {
        return $this->directoryPath;
    }

    public function setColumnNames(array $columnNames): void
    {
        $this->columnNames = $columnNames;
    }

    public function setColumnStyles(array $columnStyles): void
    {
        $this->columnStyles = $columnStyles;
    }

    public function setDirectoryPath(string $directoryPath): void
    {
        $this->directoryPath = $directoryPath;
    }
}
