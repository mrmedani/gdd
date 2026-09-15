<?php

namespace App\Domains\Contracts\Notifications;

use App\Domains\Contracts\Models\Contract;
use App\Domains\Settings\Models\Setting;
use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContractExpiryNotification extends Notification
{
    use Queueable;

    public function __construct(private Contract $contract) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $c = $this->contract;
        $locale = $notifiable->locale ?? 'fr';
        $daysLeft = $c->daysUntilExpiry();
        $dueIn = $daysLeft === 0
            ? __('alerts.today', [], $locale)
            : ($daysLeft === 1 ? __('alerts.tomorrow', [], $locale) : 'J-' . $daysLeft);

        $data = [
            'title' => $c->title,
            'party' => $c->party ?? '—',
            'end_date' => $c->end_date->format('d/m/Y'),
            'due_in' => $dueIn,
            'company_name' => Setting::get('app_name', config('app.name')),
        ];

        $template = WhatsappMessageTemplate::forType('contract_expiry');
        if ($template) {
            return $template->format($data, $locale);
        }

        return "📄 " . __('contracts.wa_title', [], $locale) . "\n"
            . "──────────────\n"
            . "📌 {$c->title}\n"
            . ($c->party ? "👤 {$c->party}\n" : '')
            . "📅 " . __('contracts.end_date', [], $locale) . " : {$c->end_date->format('d/m/Y')} ({$dueIn})";
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'contract_expiry',
            'contract_id' => $this->contract->id,
            'action_url' => url('/contracts'),
            'action_label' => __('contracts.title'),
        ];
    }
}
