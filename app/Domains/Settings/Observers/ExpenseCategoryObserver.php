<?php

namespace App\Domains\Settings\Observers;

use App\Domains\Expenses\Models\AuditLog;
use App\Domains\Expenses\Models\ExpenseCategory;

class ExpenseCategoryObserver
{
    private function log(string $action, ExpenseCategory $category, ?array $old = null, ?array $new = null): void
    {
        if (!$userId = auth()->id()) return;
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => 'category',
            'entity_id' => $category->id,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    public function created(ExpenseCategory $category): void
    {
        $this->log('created', $category, null, $category->only(['name_ar', 'name_fr', 'name_en', 'key', 'is_active']));
    }

    public function updated(ExpenseCategory $category): void
    {
        $this->log('updated', $category, $category->getOriginal(), $category->getChanges());
    }

    public function deleted(ExpenseCategory $category): void
    {
        $this->log('deleted', $category, $category->getOriginal(), null);
    }
}
