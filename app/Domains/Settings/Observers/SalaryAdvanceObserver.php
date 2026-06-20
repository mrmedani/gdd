<?php

namespace App\Domains\Settings\Observers;

use App\Domains\Employees\Models\SalaryAdvance;
use App\Domains\Expenses\Models\AuditLog;

class SalaryAdvanceObserver
{
    private function log(string $action, SalaryAdvance $advance, ?array $old = null, ?array $new = null): void
    {
        if (!$userId = auth()->id()) return;
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => 'salary_advance',
            'entity_id' => $advance->id,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    public function created(SalaryAdvance $advance): void
    {
        $this->log('created', $advance, null, $advance->only(['employee_id', 'amount', 'date', 'status', 'notes']));
    }

    public function updated(SalaryAdvance $advance): void
    {
        $this->log('updated', $advance, $advance->getOriginal(), $advance->getChanges());
    }

    public function deleted(SalaryAdvance $advance): void
    {
        $this->log('deleted', $advance, $advance->getOriginal(), null);
    }
}
