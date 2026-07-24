<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Settings\Models\Setting;
use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;

class HighExpenseNotification extends Notification
{
    use Queueable;

    public function __construct(private Collection $expenses) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();
        $total = $this->expenses->sum('amount');
        $count = $this->expenses->count();

        $template = WhatsappMessageTemplate::forType('high_expense');
        if ($template) {
            return $template->format([
                'total' => number_format($total, 2),
                'currency' => $currency,
                'count' => $count,
                'date' => now()->format('d/m/Y'),
                'threshold' => number_format((float) Setting::get('threshold', 5000), 2),
                'company_name' => Setting::get('app_name', config('app.name')),
            ]);
        }

        return "⚠️ Dépenses élevées détectées\n"
            . "──────────────\n"
            . "💰 Total : " . number_format($total, 2) . " {$currency}\n"
            . "📋 Opérations : {$count}\n"
            . "📅 Date : " . now()->format('d/m/Y');
    }

    public function toArray(object $notifiable): array
    {
        $count = $this->expenses->count();
        $total = $this->expenses->sum('amount');
        $currency = getCurrency();

        return [
            'count' => $count,
            'total_amount' => $total,
            'message_ar' => "تم اكتشاف {$count} مصروف مرتفع اليوم بإجمالي {$total} {$currency}",
            'message_fr' => "{$count} dépenses élevées détectées aujourd'hui pour un total de {$total} {$currency}",
        ];
    }
}
