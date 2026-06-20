<?php

namespace App\Policies;

use App\Domains\Expenses\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'accountant']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'accountant']);
    }

    public function view(User $user, Expense $expense): bool
    {
        return true;
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->role?->name === 'admin';
    }

    public function delete(User $user, ?Expense $expense = null): bool
    {
        return $user->role?->name === 'admin';
    }
}
