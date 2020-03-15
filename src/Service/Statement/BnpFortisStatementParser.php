<?php

declare(strict_types=1);

namespace App\Service\Statement;

final class BnpFortisStatementParser implements StatementParserInterface
{
    private const PATTERN_VALUE_DATE_SEPARATOR = '/\t\n \n/';
    private const PATTERN_VALUE_DATE = ' /\s*([0-9]{1,2}-[0-9]{1,2}-[0-9]{2,4})\s* \n/';
    private const PATTERN_TRANSACTION_SEPARATOR = '...............................................................................................................................................................................................................................................................................................................................................................................';
    private const PATTERN_TRANSACTION = "/([0-9]{4})(?:[ ]+)\t(([A-Z].*)\t\n)(([A-Z].*)\t\n)?([0-9]{2}-[0-9]{2})\t([0-9,.]*)\t(-|\+)/";

    private const TRANSACTION_TYPE_TRANSFER = 'Virement européen';
    private const TRANSACTION_TYPE_CARD_PAYMENT = 'Paiement par carte de banque';
    private const TRANSACTION_TYPE_CREDIT_REPAYMENT = 'Remboursement crédit';

    public function getValueDates(Statement $statement): array
    {
        $in = \preg_split(self::PATTERN_VALUE_DATE_SEPARATOR, $statement->getText());
        \array_shift($in); // remove head of statement

        $out = [];
        foreach ($in as $v) {
            $date = \preg_split(self::PATTERN_VALUE_DATE, $v, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $out[\trim($date[0])] = $date[1];
        }
        return $out;
    }

    public function getTransactionsByValueDate(string $valueDate): array
    {
        $in = \explode(self::PATTERN_TRANSACTION_SEPARATOR, $valueDate);

        $out = [];
        foreach ($in as $k => $v) {
            if (\preg_match_all(self::PATTERN_TRANSACTION, trim($v))) {
                $out[] = trim($v);
            }
        }
        return $out;
    }

    public function getTransactionNumber(string $operation): string
    {
        \preg_match_all(self::PATTERN_TRANSACTION, trim($operation), $matches);
        return $matches[1][0];
    }

    public function getTransactionDate(string $operation): string
    {
        \preg_match_all(self::PATTERN_TRANSACTION, trim($operation), $matches);
        return $matches[6][0];
    }

    public function getTransactionAmount(string $operation): string
    {
        \preg_match_all(self::PATTERN_TRANSACTION, trim($operation), $matches);
        return $matches[8][0] . $matches[7][0];
    }

    public function getTransactionType(string $operation): string
    {
        $content = $this->getTransactionContent($operation);

        if (\strpos($content, self::TRANSACTION_TYPE_TRANSFER) === 0) {
            return self::TRANSACTION_TYPE_TRANSFER;
        }

        if (\strpos($content, self::TRANSACTION_TYPE_CARD_PAYMENT) === 0) {
            return self::TRANSACTION_TYPE_CARD_PAYMENT;
        }

        if (\strpos($content, self::TRANSACTION_TYPE_CREDIT_REPAYMENT) === 0) {
            return self::TRANSACTION_TYPE_CREDIT_REPAYMENT;
        }

        return '';
    }

    public function getTransactionDescription(string $operation): string
    {
        $content = $this->getTransactionContent($operation);

        if (\strpos($content, self::TRANSACTION_TYPE_TRANSFER) === 0) {
            return self::TRANSACTION_TYPE_TRANSFER;
        }

        if (\strpos($content, self::TRANSACTION_TYPE_CARD_PAYMENT) === 0) {
            return self::TRANSACTION_TYPE_CARD_PAYMENT;
        }

        if (\strpos($content, self::TRANSACTION_TYPE_CREDIT_REPAYMENT) === 0) {
            return self::TRANSACTION_TYPE_CREDIT_REPAYMENT;
        }

        return '';
    }

    public function getTransactionCommunication(string $operation): string
    {
        if (self::TRANSACTION_TYPE_TRANSFER === $this->getTransactionType($operation)) {

        }

        if (self::TRANSACTION_TYPE_CARD_PAYMENT === $this->getTransactionType($operation)) {

        }

        if (self::TRANSACTION_TYPE_CREDIT_REPAYMENT === $this->getTransactionType($operation)) {

        }

        return '';
    }

    public function getTransactionContent(string $operation): string
    {
        \preg_match_all(self::PATTERN_TRANSACTION, trim($operation), $matches);
        return $matches[3][0];
    }
}
