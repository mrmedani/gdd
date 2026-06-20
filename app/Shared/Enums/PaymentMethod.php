<?php

namespace App\Shared\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
    case CreditCard = 'credit_card';
    case Other = 'other';

    public function label(): string
    {
        return __("payment_methods.{$this->value}");
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn($m) => [
            'value' => $m->value,
            'label' => $m->label(),
        ])->toArray();
    }
}
