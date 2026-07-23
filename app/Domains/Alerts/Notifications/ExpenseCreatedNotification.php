<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpenseCreatedNotification extends Notification
{

    public function __construct(private Expense $expense) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();
        $category = e($this->expense->category?->translated_name ?? $this->expense->category_key ?? '—');

        $template = WhatsappMessageTemplate::forType('expense_created');
        if ($template) {
            return $template->format([
                'description' => e($this->expense->description),
                'amount' => number_format($this->expense->amount, 2),
                'currency' => $currency,
                'category' => $category,
            ]);
        }

        return "🆕 Nouvelle dépense\n"
            . "──────────────\n"
            . "📝 " . e($this->expense->description) . "\n"
            . "💰 Montant : " . number_format($this->expense->amount, 2) . " {$currency}\n"
            . "📂 Catégorie : " . $category;
    }

    public function toArray(object $notifiable): array
    {
        $currency = getCurrency();

        return [
            'expense_id' => $this->expense->id,
            'amount' => $this->expense->amount,
            'description' => $this->expense->description,
            'message_ar' => "تم تسجيل مصروف جديد: {$this->expense->description} بقيمة {$this->expense->amount} {$currency}",
            'message_fr' => "Nouvelle dépense enregistrée: {$this->expense->description} d'un montant de {$this->expense->amount} {$currency}",
        ];
    }
}
