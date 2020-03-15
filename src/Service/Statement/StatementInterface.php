<?php

declare(strict_types=1);

namespace App\Service\Statement;

interface StatementInterface
{
    public function getText(): string;
}
