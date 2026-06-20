<?php

namespace App\Domains\Alerts\Commands;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Settings\Models\Setting;
use Illuminate\Console\Command;

class CheckBudgets extends Command
{
    protected $signature = 'alerts:check-budgets';
    protected $description = 'Check if any category has exceeded its monthly budget';

    public function handle(): int
    {
        $budgets = Setting::get('category_budgets', '{}');
        $budgets = json_decode($budgets, true) ?? [];

        if (empty($budgets)) {
            $this->info('No category budgets configured.');
            return self::SUCCESS;
        }

        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $overBudget = [];

        foreach ($budgets as $categoryId => $budget) {
            $budget = (float) $budget;
            if ($budget <= 0) continue;

            $spent = Expense::where('category_id', $categoryId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            if ($spent > $budget) {
                $category = ExpenseCategory::find($categoryId);
                $overBudget[] = [
                    'category' => $category?->translated_name ?? "#{$categoryId}",
                    'budget' => $budget,
                    'spent' => $spent,
                    'excess' => $spent - $budget,
                ];
            }
        }

        $count = count($overBudget);
        if ($count > 0 && !Alert::alreadySentToday('budget_exceeded')) {
            $categoriesList = collect($overBudget)->pluck('category')->implode(', ');
            $totalExcess = collect($overBudget)->sum('excess');

            Alert::create([
                'type' => 'budget_exceeded',
                'message_ar' => "تم تجاوز ميزانية {$count} فئة (إجمالي التجاوز: {$totalExcess} " . getCurrency() . ") : {$categoriesList}",
                'message_fr' => "{$count} catégorie(s) ont dépassé leur budget (excédent total: {$totalExcess} " . getCurrency() . ") : {$categoriesList}",
                'severity' => 'warning',
                'data' => [
                    'count' => $count,
                    'total_excess' => $totalExcess,
                    'categories' => $overBudget,
                    'action_url' => url('/expenses'),
                    'action_label' => 'Voir les dépenses',
                ],
            ]);

            $this->info("Budget exceeded alert created for {$count} category(ies).");
        } else {
            $this->info('No budget exceeded, or alert already sent today.');
        }

        return self::SUCCESS;
    }
}
