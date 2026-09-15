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

        foreach ($dueSoon as $commitment) {
            // Destinataire : le CRÉATEUR de l'engagement uniquement (pas tous les
            // utilisateurs qui ont la permission /alerts)
            $creator = $commitment->creator;
            $recipients = collect([$creator])->filter(
                fn (?User $u) => $u
                    && $u->notify_whatsapp
                    && $u->whatsapp_phone
                    && $u->whatsapp_phone !== ''
            );

            if ($recipients->isEmpty()) {
                $this->line("No WhatsApp recipient for: {$commitment->label} (creator disabled or missing phone) — skip");
                continue;
            }

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
            $this->info("Reminder sent for: {$commitment->label} (J-{$commitment->daysUntilDue()}) to creator #{$creator->id}");
        }

        return self::SUCCESS;
    }
}
