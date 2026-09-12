<?php

namespace App\Domains\Alerts\Commands;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Alerts\Models\Commitment;
use App\Domains\Alerts\Notifications\CommitmentReminderNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CommitmentReminders extends Command
{
    protected $signature = 'alerts:commitment-reminders';
    protected $description = 'Send WhatsApp + database reminders for upcoming monthly commitments (/alerts)';

    public function handle(): int
    {
        $dueSoon = Commitment::where('is_active', true)->get()
            ->filter(fn ($c) => $c->isDueSoon());

        if ($dueSoon->isEmpty()) {
            $this->info('No upcoming commitments.');
            return self::SUCCESS;
        }

        // Destinataires : rôles autorisés à voir /alerts + WhatsApp activé
        $recipients = User::where('notify_whatsapp', true)
            ->whereNotNull('whatsapp_phone')
            ->where('whatsapp_phone', '<>', '')
            ->with('role')
            ->get()
            ->filter(fn (User $u) => $u->role && $u->role->hasPermission('alerts'));

        foreach ($dueSoon as $commitment) {
            if (!Alert::alreadySentToday('commitment_reminder', ['commitment_id' => $commitment->id])) {
                Alert::create([
                    'type' => 'commitment_reminder',
                    'message_fr' => "Rappel : {$commitment->label} — échéance le {$commitment->nextDueDate()->format('d/m/Y')}",
                    'message_ar' => "تذكير: {$commitment->label} — الاستحقاق بتاريخ {$commitment->nextDueDate()->format('d/m/Y')}",
                    'severity' => 'warning',
                    'data' => [
                        'commitment_id' => $commitment->id,
                        'action_url' => url('/alerts'),
                        'action_label' => 'Voir les alertes',
                    ],
                ]);
            }

            Notification::send($recipients, new CommitmentReminderNotification($commitment));
            $this->info("Reminder sent for: {$commitment->label} (J-{$commitment->daysUntilDue()})");
        }

        return self::SUCCESS;
    }
}
