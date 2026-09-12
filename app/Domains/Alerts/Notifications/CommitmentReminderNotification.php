<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Alerts\Models\Commitment;
use App\Domains\Settings\Models\Setting;
use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommitmentReminderNotification extends Notification
{
    use Queueable;

    public function __construct(private Commitment $commitment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $c = $this->commitment;
        $currency = getCurrency();
        $locale = $notifiable->locale ?? 'fr';
        $daysLeft = $c->daysUntilDue();
        $dueIn = $daysLeft === 0
            ? __('alerts.today', [], $locale)
            : ($daysLeft === 1 ? __('alerts.tomorrow', [], $locale) : 'J-' . $daysLeft);

        $data = [
            'label' => $c->label,
            'due_date' => $c->nextDueDate()->format('d/m/Y'),
            'due_in' => $dueIn,
            'amount' => $c->amount !== null ? number_format($c->amount, 2, ',', ' ') : '—',
            'currency' => $currency,
            'company_name' => Setting::get('app_name', config('app.name')),
        ];

        $template = WhatsappMessageTemplate::forType('commitment_reminder');
        if ($template) {
            return $template->format($data, $locale);
        }

        return "⏰ Rappel d'échéance\n"
            . "──────────────\n"
            . "📌 {$c->label}\n"
            . "📅 Échéance : {$c->nextDueDate()->format('d/m/Y')} ({$dueIn})";
    }

    public function toArray(object $notifiable): array
    {
        $c = $this->commitment;
        $currency = getCurrency();
        $due = $c->nextDueDate()->format('d/m/Y');

        return [
            'commitment_id' => $c->id,
            'message_ar' => "تذكير: استحقاق {$c->label} بتاريخ {$due}",
            'message_fr' => "Rappel : échéance {$c->label} le {$due} ({$currency})",
        ];
    }
}
