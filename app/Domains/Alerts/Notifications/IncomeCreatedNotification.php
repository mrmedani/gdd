<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Settings\Models\Setting;
use App\Domains\Settings\Models\WhatsappMessageTemplate;
use App\Domains\Treasury\Models\Income;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IncomeCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private Income $income) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();
        $template = WhatsappMessageTemplate::forType('income_created');
        if ($template) {
            return $template->format([
                'source_name' => e($this->income->source_name ?? '—'),
                'source_type' => Income::sourceTypeLabel($this->income->source_type),
                'amount' => number_format((float) $this->income->amount, 2, ',', ' '),
                'currency' => $currency,
                'date' => $this->income->date,
                'company_name' => Setting::get('app_name', config('app.name')),
            ]);
        }

        return "💰 Nouvelle entrée d'argent\n"
            . "──────────────\n"
            . "📝 " . e($this->income->source_name ?? '—') . "\n"
            . "🏷️ Type : " . Income::sourceTypeLabel($this->income->source_type) . "\n"
            . "💵 Montant : " . number_format((float) $this->income->amount, 2, ',', ' ') . " {$currency}\n"
            . "📅 Date : " . $this->income->date;
    }

    public function toArray(object $notifiable): array
    {
        $currency = getCurrency();

        return [
            'income_id' => $this->income->id,
            'amount' => $this->income->amount,
            'source_name' => $this->income->source_name,
            'source_type' => $this->income->source_type,
            'message_ar' => "تم تسجيل إيراد جديد: " . e($this->income->source_name ?? '—') . " بقيمة " . number_format((float) $this->income->amount, 2, ',', ' ') . " {$currency}",
            'message_fr' => "Nouvelle entrée d'argent enregistrée: " . e($this->income->source_name ?? '—') . " d'un montant de " . number_format((float) $this->income->amount, 2, ',', ' ') . " {$currency}",
        ];
    }
}
