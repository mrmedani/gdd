<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Expenses\Models\Expense;
use Illuminate\Notifications\Notification;

class ExpenseDeletedNotification extends Notification
{
    public function __construct(private Expense $expense) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();

        return "🗑️ Dépense supprimée\n"
            . "──────────────\n"
            . "📝 " . e($this->expense->description) . "\n"
            . "💰 Montant : " . number_format($this->expense->amount, 2) . " {$currency}";
    }

    public function toArray(object $notifiable): array
    {
        $currency = getCurrency();
        return [
            'expense_id' => $this->expense->id,
            'amount' => $this->expense->amount,
            'description' => $this->expense->description,
            'message_ar' => "تم حذف المصروف: {$this->expense->description} بقيمة {$this->expense->amount} {$currency}",
            'message_fr' => "Dépense supprimée: {$this->expense->description} d'un montant de {$this->expense->amount} {$currency}",
        ];
    }
}
