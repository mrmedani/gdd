<?php

namespace App\Domains\Contracts\Commands;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Contracts\Models\Contract;
use App\Domains\Contracts\Notifications\ContractExpiryNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ContractReminders extends Command
{
    protected $signature = 'contracts:reminders';
    protected $description = 'Send WhatsApp + in-app reminders J-3 for contracts about to expire (per creator)';

    public function handle(): int
    {
        $expiring = Contract::where('reminder_sent_at', false)
            ->whereDate('end_date', '>=', now()->startOfDay())
            ->get()
            ->filter(fn ($c) => $c->isExpiringSoon());

        if ($expiring->isEmpty()) {
            $this->info('No contracts expiring in ≤ 3 days.');
            return self::SUCCESS;
        }

        foreach ($expiring as $contract) {
            // Destinataire : le CRÉATEUR du contrat uniquement (cohérent avec /alerts)
            $creator = $contract->creator;
            if (!$creator || !$creator->notify_whatsapp || !$creator->whatsapp_phone) {
                $this->line("No WhatsApp recipient for contract #{$contract->id} — skip notification (alert in-app still created)");
            }

            $daysLeft = $contract->daysUntilExpiry();
            $when = $daysLeft === 0 ? __('alerts.today') : ($daysLeft === 1 ? __('alerts.tomorrow') : 'J-' . $daysLeft);

            // Alerte in-app (visible dans les notifications de l'app)
            if (!Alert::alreadySentToday('contract_expiry', ['contract_id' => $contract->id])) {
                Alert::create([
                    'type' => 'contract_expiry',
                    'message_fr' => "Contrat « {$contract->title} » arrive à échéance le {$contract->end_date->format('d/m/Y')} ({$when})",
                    'message_ar' => "العقد « {$contract->title} » ينتهي بتاريخ {$contract->end_date->format('d/m/Y')} ({$when})",
                    'severity' => $daysLeft <= 1 ? 'danger' : 'warning',
                    'data' => [
                        'contract_id' => $contract->id,
                        'action_url' => url('/contracts'),
                        'action_label' => 'Voir les contrats',
                    ],
                ]);
            }

            // WhatsApp au créateur uniquement
            if ($creator && $creator->notify_whatsapp && $creator->whatsapp_phone) {
                Notification::send(collect([$creator]), new ContractExpiryNotification($contract));
                $this->info("Reminder sent for contract #{$contract->id} ({$daysLeft}d) to creator #{$creator->id}");
            } else {
                $this->line("Contract #{$contract->id}: in-app alert only (creator has no WhatsApp).");
            }

            // Dedup définitif : une seule alerte J-3 par contrat, jamais re-sent
            $contract->update(['reminder_sent_at' => true]);
        }

        return self::SUCCESS;
    }
}
