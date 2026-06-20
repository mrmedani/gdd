<?php

namespace App\Policies;

use App\Domains\Expenses\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('expenses');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('expenses');
    }

    public function view(User $user, Expense $expense): bool
    {
        return true;
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->hasPermission('expenses');
    }

    public function delete(User $user, ?Expense $expense = null): bool
    {
        return $user->hasPermission('expenses');
    }
}
