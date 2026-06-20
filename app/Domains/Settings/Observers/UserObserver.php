<?php

namespace App\Domains\Settings\Observers;

use App\Domains\Expenses\Models\AuditLog;
use App\Models\User;

class UserObserver
{
    private array $tracked = ['name', 'email', 'role_id', 'locale'];

    private function log(string $action, User $user, ?array $old = null, ?array $new = null): void
    {
        if (!$userId = auth()->id()) return;
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    public function created(User $user): void
    {
        $this->log('created', $user, null, $user->only($this->tracked));
    }

    public function updated(User $user): void
    {
        $changes = array_intersect_key($user->getChanges(), array_flip($this->tracked));
        if (empty($changes)) return;
        $old = array_intersect_key($user->getOriginal(), $changes);
        $this->log('updated', $user, $old, $changes);
    }

    public function deleted(User $user): void
    {
        $this->log('deleted', $user, $user->only($this->tracked), null);
    }
}
