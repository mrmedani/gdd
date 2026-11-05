<?php

namespace App\Domains\Expenses\Observers;

use App\Domains\Alerts\Notifications\ExpenseCreatedNotification;
use App\Domains\Alerts\Notifications\ExpenseModifiedNotification;
use App\Domains\Alerts\Notifications\ExpenseDeletedNotification;
use App\Domains\Employees\Models\SalaryAdvance;
use App\Domains\Expenses\Models\AuditLog;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Statistics\Support\StatsCacheInvalidator;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ExpenseObserver
{
    /**
     * Flag de SILENCE notification (import CSV en masse) : quand true, les
     * notifications WhatsApp/alertes par ligne sont SUPPRIMÉES — il faut
     * (à l'import) 1 seule notification récapitulative à la fin, sinon
     * 20 lignes importées = 20 messages aux admins = spam+anti-spam.
     * AuditLog + invalidations de caches restent actifs (traçabilité cohérente).
     */
    public static bool $skipNotifications = false;

    private function notifyAdmins(object $notification): void
    {
        try {
            $recipients = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
                ->orWhere('notify_whatsapp', true)
                ->get();
            foreach ($recipients as $recipient) {
                try {
                    Notification::sendNow($recipient, $notification);
                } catch (\Throwable $e) {
                    Log::warning('Failed to send notification to user ' . $recipient->id . ': ' . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to prepare notifications: ' . $e->getMessage());
        }
    }

    private function filterFields(Expense|array $data): array
    {
        $fields = ['amount', 'description', 'category_id', 'category_key', 'payment_method', 'date', 'notes'];
        if ($data instanceof Expense) {
            return $data->only($fields);
        }
        return array_intersect_key($data, array_flip($fields));
    }

    public function created(Expense $expense): void
    {
        // INVALIDATION IA : la saisie fraîche doit être visible à l'assistant immédiatement
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateExpenses();
        \App\Domains\Statistics\Support\StatsCacheInvalidator::bump();

        if ($userId = auth()->id()) {
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'created',
                'entity_type' => 'expense',
                'entity_id' => $expense->id,
                'new_values' => $this->filterFields($expense),
            ]);
        }

        $this->deductAdvances($expense);

        if (!self::$skipNotifications) {
            $this->notifyAdmins(new ExpenseCreatedNotification($expense));
        }

        Log::info('Expense created', [
            'id' => $expense->id,
            'user_id' => auth()->id() ?? $expense->created_by,
        ]);
    }

    public function updated(Expense $expense): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateExpenses();
        \App\Domains\Statistics\Support\StatsCacheInvalidator::bump();

        if ($userId = auth()->id()) {
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'updated',
                'entity_type' => 'expense',
                'entity_id' => $expense->id,
                'old_values' => $this->filterFields($expense->getOriginal()),
                'new_values' => $this->filterFields($expense->getChanges()),
            ]);
        }

        Log::info('Expense updated', [
            'id' => $expense->id,
            'user_id' => auth()->id(),
        ]);

        if (!self::$skipNotifications) {
            $this->notifyAdmins(new ExpenseModifiedNotification($expense, $expense->getChanges()));
        }
    }

    public function deleted(Expense $expense): void
    {
        \App\Domains\AI\Support\AiCacheInvalidator::invalidateExpenses();
        \App\Domains\Statistics\Support\StatsCacheInvalidator::bump();

        if ($userId = auth()->id()) {
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'deleted',
                'entity_type' => 'expense',
                'entity_id' => $expense->id,
                'old_values' => $this->filterFields($expense->getOriginal()),
            ]);
        }

        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        Log::info('Expense deleted', [
            'id' => $expense->id,
            'user_id' => auth()->id(),
        ]);

        if (!self::$skipNotifications) {
            $this->notifyAdmins(new ExpenseDeletedNotification($expense));
        }
    }

    private function deductAdvances(Expense $expense): void
    {
        if (Expense::$skipSalaryAdvanceDeduction) {
            return;
        }

        if (
            ($expense->category_key ?? '') !== 'salaries'
            || !$expense->employee_id
            || $expense->amount <= 0
        ) {
            return;
        }

        $advances = SalaryAdvance::where('employee_id', $expense->employee_id)
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('date', 'asc')
            ->get();

        $remaining = (float) $expense->amount;

        foreach ($advances as $advance) {
            if ($remaining <= 0) break;

            $advAmount = (float) $advance->amount;

            if ($advAmount <= $remaining) {
                $advance->update(['status' => 'deducted']);
                $remaining -= $advAmount;
            } else {
                $advance->update([
                    'status' => 'deducted',
                    'amount' => $remaining,
                ]);
                $remaining = 0;
            }
        }
    }

}
