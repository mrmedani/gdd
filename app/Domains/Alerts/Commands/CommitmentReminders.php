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
        // CASCADE : WhatsApp uniquement à J-lead / J-7 / J-3 / J-1 / J-0
        // (avant : envoi quotidien pendant toute la fenêtre = spam)
        $dueSoon = Commitment::where('is_active', true)->get()
            ->filter(fn ($c) => $c->isDueSoon() && $c->isReminderDay());

        if ($dueSoon->isEmpty()) {
            $this->info('No upcoming commitments.');
            return self::SUCCESS;
        }

        foreach ($dueSoon as $commitment) {
            // Destinataires : le CRÉATEUR + les ADMINISTRATEURS (secours si le
            // créateur est absent / WhatsApp désactivé). Chacun filtré sur ses
            // préférences notify_whatsapp + numéro renseigné.
            $recipients = collect([$commitment->creator])
                ->merge(User::whereHas('role', fn ($r) => $r->name === 'admin')->get())
                ->unique('id')
                ->filter(
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
            $this->info("Reminder sent for: {$commitment->label} (J-{$commitment->daysUntilDue()}) to " . $recipients->pluck('id')->implode(','));
        }

        return self::SUCCESS;
    }
}
