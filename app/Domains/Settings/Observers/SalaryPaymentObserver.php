<?php

namespace App\Domains\Settings\Observers;

use App\Domains\Employees\Models\SalaryPayment;
use App\Domains\Expenses\Models\AuditLog;

class SalaryPaymentObserver
{
    private function log(string $action, SalaryPayment $payment, ?array $old = null, ?array $new = null): void
    {
        if (!$userId = auth()->id()) return;
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => 'salary_payment',
            'entity_id' => $payment->id,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    public function created(SalaryPayment $payment): void
    {
        $this->log('created', $payment, null, $payment->only(['employee_id', 'month', 'year', 'base_amount', 'advances_deducted', 'net_amount', 'payment_method']));
    }

    public function deleted(SalaryPayment $payment): void
    {
        $this->log('deleted', $payment, $payment->getOriginal(), null);
    }
}
