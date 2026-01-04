<?php

namespace App\Enums\Purchase;

use App\Enums\Traits\EnumToArray;

enum PaymentMethod: string
{
    use EnumToArray;

    case Cash = 'cash';
    case CreditCard = 'credit_card';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
}
