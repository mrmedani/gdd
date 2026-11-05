<?php

namespace App\Domains\Reports\Support;

use App\Domains\Expenses\Models\Expense;
use Illuminate\Support\Collection;

/**
 * Agrégateur des rapports — SOURCE UNIQUE des ventilations utilisées par
 * les 3 rapports (mensuel, annuel, personnalisé). Avant, byCategory /
 * byPaymentMethod / byEmployee étaient recopiés entre mensuel et annuel.
 */
class ReportAggregator
{
    /** Ventilation par catégorie (regroupée sur le parent si existant). */
    public static function byCategory(Collection $expenses, float $total): Collection
    {
        return $expenses->groupBy(fn (Expense $e) => $e->category?->parent?->translated_name
                ?? $e->category?->translated_name
                ?? __("categories.{$e->category_key}"))
            ->map(fn (Collection $items) => [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => $total > 0 ? round(($items->sum('amount') / $total) * 100, 1) : 0,
            ])
            ->sortByDesc('total');
    }

    /** Ventilation par moyen de paiement. */
    public static function byPaymentMethod(Collection $expenses): Collection
    {
        return $expenses->groupBy('payment_method')
            ->map(fn (Collection $items) => [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ]);
    }

    /** Ventilation par employé (les nulles regroupées sous « sans employé »). */
    public static function byEmployee(Collection $expenses, float $total): Collection
    {
        return $expenses->groupBy(fn (Expense $e) => $e->employee?->name ?? __('expenses.no_employee'))
            ->map(fn (Collection $items, string $emp) => [
                'name' => $emp,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => $total > 0 ? round(($items->sum('amount') / $total) * 100, 1) : 0,
            ])
            ->sortByDesc('total');
    }

    /** Détail jour par jour (utilisé par les rapports mensuel + personnalisé). */
    public static function dailyBreakdown(Collection $expenses, \Carbon\Carbon $rangeStart, \Carbon\Carbon $rangeEnd): Collection
    {
        $dailyTotals = $expenses->groupBy(fn (Expense $e) => $e->date->format('Y-m-d'))
            ->map(fn (Collection $items) => [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ]);

        $breakdown = collect();
        $cursor = $rangeStart->copy();
        while ($cursor->lte($rangeEnd)) {
            $key = $cursor->format('Y-m-d');
            $breakdown->push([
                'date' => $cursor->copy(),
                'label' => $cursor->translatedFormat('D d'),
                'total' => (float) ($dailyTotals[$key]['total'] ?? 0),
                'count' => (int) ($dailyTotals[$key]['count'] ?? 0),
            ]);
            $cursor->addDay();
        }
        return $breakdown;
    }

    /** Requête de base avec les 3 filtres partagés (catégorie, employé, paiement). */
    public static function applyFilters($query, array $data): void
    {
        if (!empty($data['category_id'])) {
            $ids = \App\Domains\Expenses\Models\ExpenseCategory::where('id', $data['category_id'])
                ->orWhere('parent_id', $data['category_id'])
                ->pluck('id');
            $query->whereIn('category_id', $ids);
        }
        if (!empty($data['employee_id'])) {
            $query->where('employee_id', $data['employee_id']);
        }
        if (!empty($data['payment_method'])) {
            $query->where('payment_method', $data['payment_method']);
        }
    }
}
