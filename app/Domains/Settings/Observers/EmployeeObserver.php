<?php

namespace App\Domains\Settings\Observers;

use App\Domains\Employees\Models\Employee;
use App\Domains\Expenses\Models\AuditLog;

class EmployeeObserver
{
    private function log(string $action, Employee $employee, ?array $old = null, ?array $new = null): void
    {
        if (!$userId = auth()->id()) return;
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => 'employee',
            'entity_id' => $employee->id,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    public function created(Employee $employee): void
    {
        $this->log('created', $employee, null, $employee->only(['name', 'email', 'phone', 'role_title', 'base_salary', 'status']));
    }

    public function updated(Employee $employee): void
    {
        $this->log('updated', $employee, $employee->getOriginal(), $employee->getChanges());
    }

    public function deleted(Employee $employee): void
    {
        $this->log('deleted', $employee, $employee->getOriginal(), null);
    }
}
