<?php

declare(strict_types=1);

namespace App\Service\Statement;

use Smalot\PdfParser\Document;
use Smalot\PdfParser\Parser;

final class Statement implements StatementInterface
{
    /** @var Document */
    private $file;
    /** @var string */
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getText(): string
    {
        try {
            $this->file = (new Parser())->parseFile($this->filePath);
        } catch (\Exception $e) {
            // TODO : log
            dd($e->getMessage());
        }

        return $this->file->getText();
    }
}
