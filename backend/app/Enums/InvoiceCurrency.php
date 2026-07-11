<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceCurrency: string
{
    case UAH = 'UAH';
    case USD = 'USD';
    case EUR = 'EUR';
}
