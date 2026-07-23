<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Settings\Models\WhatsappMessageTemplate;
use App\Domains\Treasury\Models\MonthlyClosure;
use Illuminate\Notifications\Notification;

class MonthlyClosureNotification extends Notification
{
    public function __construct(private MonthlyClosure $closure) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();
        $label = formatPeriodLabel($this->closure->month);

        $template = WhatsappMessageTemplate::forType('monthly_closure');
        if ($template) {
            return $template->format([
                'period' => $label,
                'gains' => number_format($this->closure->gains, 2),
                'currency' => $currency,
                'expenses' => number_format($this->closure->expenses, 2),
                'balance' => number_format($this->closure->balance, 2),
            ]);
        }

        return "🔒 Clôture de période\n"
            . "──────────────\n"
            . "📆 Période : {$label}\n"
            . "📈 Gains : " . number_format($this->closure->gains, 2) . " {$currency}\n"
            . "📉 Dépenses : " . number_format($this->closure->expenses, 2) . " {$currency}\n"
            . "⚖️ Solde : " . number_format($this->closure->balance, 2) . " {$currency}";
    }

    public function toArray(object $notifiable): array
    {
        $currency = getCurrency();
        $label = formatPeriodLabel($this->closure->month);
        return [
            'month' => $this->closure->month,
            'balance' => $this->closure->balance,
            'message_ar' => "تم إغلاق فترة {$label} برصيد {$this->closure->balance} {$currency}",
            'message_fr' => "Clôture de la période {$label} effectuée avec un solde de {$this->closure->balance} {$currency}",
        ];
    }
}
