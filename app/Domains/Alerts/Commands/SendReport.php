<?php

namespace App\Domains\Alerts\Commands;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Alerts\Notifications\DailyReportNotification;
use App\Domains\Expenses\Models\Expense;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendReport extends Command
{
    protected $signature = 'alerts:report {period=daily : daily, weekly, or monthly}';
    protected $description = 'Send expense report for the given period';

    public function handle(): int
    {
        $period = $this->argument('period');
        $now = now();

        $start = match ($period) {
            'weekly' => $now->copy()->startOfWeek(),
            'monthly' => $now->copy()->startOfMonth(),
            default => $now->copy()->startOfDay(),
        };

        $expenses = Expense::whereBetween('created_at', [$start, $now])->get();
        $stats = [
            'total_expenses' => $expenses->sum('amount'),
            'expense_count' => $expenses->count(),
            'period_start' => $start->format('d/m/Y'),
            'period_end' => $now->format('d/m/Y'),
        ];

        if ($expenses->isEmpty()) {
            $this->info("No expenses for {$period} period, skipping report");
            return self::SUCCESS;
        }

        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->orWhere('notify_whatsapp', true)
            ->get();

        if ($admins->isNotEmpty()) {
            $label = match ($period) {
                'weekly' => 'hebdomadaire',
                'monthly' => 'mensuel',
                default => 'quotidien',
            };

            if (!Alert::alreadySentToday("{$period}_report")) {
                Alert::create([
                    'type' => "{$period}_report",
                    'message_ar' => "تقرير {$label}: {$stats['expense_count']} مصاريف بإجمالي {$stats['total_expenses']} " . getCurrency(),
                    'message_fr' => "Rapport {$label}: {$stats['expense_count']} dépenses pour un total de {$stats['total_expenses']} " . getCurrency(),
                    'severity' => 'info',
                    'data' => array_merge($stats, ['action_url' => url('/expenses'), 'action_label' => 'Voir le rapport']),
                ]);
            }

            Notification::send($admins, new DailyReportNotification($stats, $period));
            $this->info("{$label} report sent to " . $admins->count() . " admin(s)");
        }

        return self::SUCCESS;
    }
}
