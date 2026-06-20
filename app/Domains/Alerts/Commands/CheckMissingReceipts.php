<?php

namespace App\Domains\Alerts\Commands;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Settings\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;

class CheckMissingReceipts extends Command
{
    protected $signature = 'alerts:missing-receipts';
    protected $description = 'Alert when expenses older than 3 days have no receipt';

    public function handle(): int
    {
        $days = (int) Setting::get('missing_receipt_days', 3);

        $missingReceipts = Expense::whereNull('receipt_path')
            ->whereDate('date', '<=', now()->subDays($days))
            ->whereDate('date', '>=', now()->subDays(30))
            ->count();

        if ($missingReceipts > 0 && !Alert::alreadySentToday('missing_receipts')) {
            Alert::create([
                'type' => 'missing_receipts',
                'message_ar' => "يوجد {$missingReceipts} مصروف(ف) بدون إيصال منذ أكثر من {$days} أيام",
                'message_fr' => "{$missingReceipts} dépense(s) sans reçu depuis plus de {$days} jours",
                'severity' => 'warning',
                'data' => [
                    'count' => $missingReceipts,
                    'days_threshold' => $days,
                    'action_url' => url('/expenses'),
                    'action_label' => 'Voir les dépenses',
                ],
            ]);

            $this->info("Alert created for {$missingReceipts} expenses without receipt.");
        } else {
            $this->info('No expenses without receipt, or alert already sent today.');
        }

        return self::SUCCESS;
    }
}
