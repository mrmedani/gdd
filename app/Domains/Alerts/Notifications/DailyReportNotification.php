<?php

namespace App\Domains\Alerts\Notifications;

use Illuminate\Notifications\Notification;

class DailyReportNotification extends Notification
{
    public function __construct(
        private array $stats,
        private string $period,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();
        $total = $this->stats['total_expenses'] ?? 0;
        $count = $this->stats['expense_count'] ?? 0;
        $periodLabel = $this->period === 'daily' ? 'Journalier' : ($this->period === 'weekly' ? 'Hebdomadaire' : 'Mensuel');

        return "📊 Rapport {$periodLabel}\n"
            . "──────────────\n"
            . "💵 Dépenses : " . number_format($total, 2) . " {$currency}\n"
            . "📋 Opérations : {$count}\n"
            . "📅 " . ($this->stats['period_start'] ?? '') . " → " . ($this->stats['period_end'] ?? '');
    }

    public function toArray(object $notifiable): array
    {
        $currency = getCurrency();
        $count = $this->stats['expense_count'] ?? 0;
        $total = $this->stats['total_expenses'] ?? 0;
        return [
            'total_expenses' => $total,
            'expense_count' => $count,
            'message_ar' => "تقرير {$this->period}: {$count} مصاريف بإجمالي {$total} {$currency}",
            'message_fr' => "Rapport {$this->period}: {$count} dépenses pour un total de {$total} {$currency}",
        ];
    }
}
