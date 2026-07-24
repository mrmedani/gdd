<?php

namespace App\Domains\Alerts\Notifications;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Settings\Models\Setting;
use App\Domains\Settings\Models\WhatsappMessageTemplate;
use Illuminate\Notifications\Notification;

class ExpenseModifiedNotification extends Notification
{
    public function __construct(private Expense $expense, private array $changes) {}

    public function via(object $notifiable): array
    {
        return ['database', 'whatsapp'];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $currency = getCurrency();
        $category = e($this->expense->category?->translated_name ?? $this->expense->category_key ?? '—');

        $template = WhatsappMessageTemplate::forType('expense_modified');
        if ($template) {
            return $template->format([
                'description' => e($this->expense->description),
                'amount' => number_format($this->expense->amount, 2),
                'currency' => $currency,
                'category' => $category,
                'date' => $this->expense->date,
                'payment_method' => __('payment_methods.' . $this->expense->payment_method),
                'company_name' => Setting::get('app_name', config('app.name')),
            ]);
        }

        return "✏️ Dépense modifiée\n"
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
            'changes' => $this->changes,
            'message_ar' => "تم تعديل المصروف: {$this->expense->description} بقيمة {$this->expense->amount} {$currency}",
            'message_fr' => "Dépense modifiée: {$this->expense->description} d'un montant de {$this->expense->amount} {$currency}",
        ];
    }
}
