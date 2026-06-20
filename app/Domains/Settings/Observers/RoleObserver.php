<?php

namespace App\Domains\Settings\Observers;

use App\Domains\Expenses\Models\AuditLog;
use App\Models\Role;

class RoleObserver
{
    public function updated(Role $role): void
    {
        if (!$userId = auth()->id()) return;

        $changes = $role->getChanges();
        if (isset($changes['permissions'])) {
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'updated',
                'entity_type' => 'role',
                'entity_id' => $role->id,
                'old_values' => ['permissions' => $role->getOriginal('permissions')],
                'new_values' => ['permissions' => $role->permissions],
            ]);
        }
    }
}
