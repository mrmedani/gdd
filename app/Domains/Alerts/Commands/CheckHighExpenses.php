<?php

namespace App\Domains\Alerts\Commands;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Alerts\Notifications\HighExpenseNotification;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Settings\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckHighExpenses extends Command
{
    protected $signature = 'alerts:high-expenses';
    protected $description = 'Check for high expenses today';

    public function handle(): int
    {
        $threshold = (float) Setting::get('high_expense_threshold', config('app.high_expense_threshold', 5000));

        $highExpenses = Expense::whereDate('created_at', today())
            ->where('amount', '>=', $threshold)
            ->get();

        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->orWhere('notify_whatsapp', true)
            ->get();

        $count = $highExpenses->count();
        if ($count > 0) {
            $totalAmount = $highExpenses->sum('amount');

            if (!Alert::alreadySentToday('high_expense')) {
                Alert::create([
                    'type' => 'high_expense',
                    'message_ar' => "تم اكتشاف {$count} مصروف مرتفع اليوم بإجمالي {$totalAmount} " . getCurrency(),
                    'message_fr' => "{$count} dépenses élevées détectées aujourd'hui pour un total de {$totalAmount} " . getCurrency(),
                    'severity' => 'warning',
                    'data' => [
                        'count' => $count,
                        'total_amount' => $totalAmount,
                        'expenses' => $highExpenses->map(fn($e) => ['id' => $e->id, 'amount' => $e->amount, 'description' => $e->description])->toArray(),
                        'action_url' => url('/expenses'),
                        'action_label' => 'Voir les dépenses',
                    ],
                ]);
            }

            Notification::send($admins, new HighExpenseNotification($highExpenses));
            $this->info("{$count} high expense(s) detected");
        } else {
            $this->info('No high expenses today');
        }

        return self::SUCCESS;
    }
}
