<?php

declare(strict_types=1);

namespace App\Service\Statement;

interface StatementParserInterface
{
    public function getValueDates(Statement $statement): array;
    public function getTransactionsByValueDate(string $valueDate): array;
    public function getTransactionNumber(string $operation): string;
    public function getTransactionDate(string $operation): string;
    public function getTransactionDescription(string $operation): string;
    public function getTransactionContent(string $operation): string;
    public function getTransactionAmount(string $operation): string;
    public function getTransactionType(string $operation): string;
    public function getTransactionCommunication(string $operation): string;
}