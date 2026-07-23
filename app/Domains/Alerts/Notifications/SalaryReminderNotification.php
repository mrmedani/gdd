<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SalaryReminderNotification extends Notification
{
    use Queueable;

    public function __construct(private int $count, private float $totalSalary) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();

        $template = WhatsappMessageTemplate::forType('salary_reminder');
        if ($template) {
            return $template->format([
                'count' => $this->count,
                'total' => number_format($this->totalSalary, 2),
                'currency' => $currency,
                'date' => now()->format('d/m/Y'),
            ]);
        }

        return "💰 Rappel de paie\n"
            . "──────────────\n"
            . "👥 Employés : {$this->count}\n"
            . "💵 Total : " . number_format($this->totalSalary, 2) . " {$currency}\n"
            . "📅 Date : " . now()->format('d/m/Y');
    }

    public function toArray(object $notifiable): array
    {
        $currency = getCurrency();

        return [
            'count' => $this->count,
            'total_salary' => $this->totalSalary,
            'message_ar' => "تذكير: حان موعد صرف الرواتب الشهري لـ {$this->count} موظف بإجمالي {$this->totalSalary} {$currency}",
            'message_fr' => "Rappel: C'est le jour de paie pour {$this->count} employés avec un total de {$this->totalSalary} {$currency}",
        ];
    }
}
