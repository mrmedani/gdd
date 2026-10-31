<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Settings\Models\Setting;
use App\Domains\Settings\Models\WhatsappMessageTemplate;
use App\Domains\Treasury\Models\StationCharge;
use Illuminate\Notifications\Notification;

class StationChargeNotification extends Notification
{
    public function __construct(private StationCharge $charge, private string $action = 'created') {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();
        $action = match ($this->action) {
            'updated' => 'station_charge_updated',
            'deleted' => 'station_charge_deleted',
            default => 'station_charge_created',
        };

        $template = WhatsappMessageTemplate::forType($action);
        if ($template) {
            return $template->format([
                'station' => e($this->charge->station_name),
                'period' => formatPeriodLabel($this->charge->period),
                'amount' => number_format($this->charge->amount, 2),
                'currency' => $currency,
                'user' => e($this->charge->creator?->name ?? '—'),
                'company_name' => Setting::get('app_name', config('app.name')),
            ]);
        }

        $head = match ($this->action) {
            'updated' => '✏️ Commission station modifiée',
            'deleted' => '🗑️ Commission station supprimée',
            default => '🆕 Nouvelle commission station',
        };

        return $head . "\n"
            . "──────────────\n"
            . "🏢 Station : " . e($this->charge->station_name) . "\n"
            . "📆 Période : " . formatPeriodLabel($this->charge->period) . "\n"
            . "💰 Montant : " . number_format($this->charge->amount, 2) . " {$currency}";
    }

    public function toArray(object $notifiable): array
    {
        $currency = getCurrency();
        $label = formatPeriodLabel($this->charge->period);

        return [
            'station_charge_id' => $this->charge->id,
            'action' => $this->action,
            'amount' => $this->charge->amount,
            'message_fr' => match ($this->action) {
                'updated' => "Commission station modifiée : {$this->charge->station_name} — {$label} ({$this->charge->amount} {$currency})",
                'deleted' => "Commission station supprimée : {$this->charge->station_name} — {$label}",
                default => "Nouvelle commission station : {$this->charge->station_name} — {$label} ({$this->charge->amount} {$currency})",
            },
            'message_ar' => match ($this->action) {
                'updated' => "تم تعديل عمولة المحطة: {$this->charge->station_name} — {$label} ({$this->charge->amount} {$currency})",
                'deleted' => "تم حذف عمولة المحطة: {$this->charge->station_name} — {$label}",
                default => "عمولة محطة جديدة: {$this->charge->station_name} — {$label} ({$this->charge->amount} {$currency})",
            },
            'action_url' => route('treasury.station-charges'),
            'action_label' => __('station_charges.title', ['default' => 'Commissions stations']),
        ];
    }
}
