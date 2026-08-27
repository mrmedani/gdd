<?php

namespace App\Domains\Treasury\Observers;

use App\Domains\Alerts\Notifications\IncomeCreatedNotification;
use App\Domains\Alerts\Notifications\IncomeDeletedNotification;
use App\Domains\Alerts\Notifications\IncomeModifiedNotification;
use App\Domains\Treasury\Models\Income;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class IncomeObserver
{
    /**
     * Destinataires = uniquement les rôles autorisés à voir /incomes
     * (permission 'incomes' = true) ET ayant activé la notif WhatsApp + un n° de téléphone.
     */
    private function notifyIncomesRoles(object $notification): void
    {
        try {
            $recipients = User::where('notify_whatsapp', true)
                ->whereNotNull('whatsapp_phone')
                ->where('whatsapp_phone', '<>', '')
                ->with('role')
                ->get()
                ->filter(fn(User $u) => $u->role && $u->role->hasPermission('incomes'));

            foreach ($recipients as $recipient) {
                try {
                    Notification::sendNow($recipient, $notification);
                } catch (\Throwable $e) {
                    Log::warning('Failed to send income notification to user ' . $recipient->id . ': ' . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to prepare income notifications: ' . $e->getMessage());
        }
    }

    public function created(Income $income): void
    {
        $this->notifyIncomesRoles(new IncomeCreatedNotification($income));
    }

    public function updated(Income $income): void
    {
        $this->notifyIncomesRoles(new IncomeModifiedNotification($income));
    }

    public function deleted(Income $income): void
    {
        $this->notifyIncomesRoles(new IncomeDeletedNotification($income));
    }
}
