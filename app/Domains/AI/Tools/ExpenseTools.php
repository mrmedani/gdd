<?php

namespace App\Domains\AI\Tools;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Treasury\Models\Income;
use App\Domains\Treasury\Models\MonthlyClosure;
use Illuminate\Support\Facades\DB;

/**
 * Agrégats sûrs exposés à l'IA (aucun SQL brut, aucune donnée sensible par ligne).
 * L'IA reçoit des totaux et synthèses — y compris l'HISTORIQUE des périodes passées.
 *
 * Les dépenses récurrentes sont listées AVEC LES PÉRIODES PRÉCISES où elles apparaissent,
 * sinon l'IA ne peut pas répondre à "lesquelles en juillet-août ?" (bug constaté en prod).
 */
class ExpenseTools
{
    /** Nombre de périodes passées incluses dans le contexte (période courante + 6 précédentes). */
    protected int $historyPeriods = 6;

    public function buildContext(): string
    {
        $currency = getCurrency();
        $currentPeriod = getPeriodFromDate(now());

        // Les périodes couvertes (courante + N précédentes non vides)
        $periods = [$currentPeriod];
        $period = $currentPeriod;
        for ($i = 0; $i < $this->historyPeriods; $i++) {
            $period = $this->previousPeriod($period);
            if (!$this->periodIsEmpty($period)) {
                $periods[] = $period;
            }
        }

        // Carte des dépenses récurrentes : desc_norm => [périodes précises + montants]
        $recurring = $this->recurringMap($periods);

        $lines = [];
        foreach ($periods as $p) {
            $lines[] = $this->periodSummary($p, $currency, $p === $currentPeriod, $recurring);
        }

        $lines[] = $this->recurringSummary($recurring, $currency);

        $lastClosure = MonthlyClosure::orderByDesc('created_at')->first();
        if ($lastClosure) {
            $lines[] = "Dernière clôture de trésorerie : " . number_format((float) $lastClosure->balance, 2, ',', ' ') . " $currency";
        }

        return implode("\n\n", $lines);
    }

    /**
     * Résumé d'une période : totaux OBLIGATOIRES (dépenses/entrées/gain) + top catégories
     * + les dépenses récurrentes vues PRÉCISÉMENT dans cette période (avec montant de la période).
     */
    protected function periodSummary(string $period, string $currency, bool $isCurrent, array $recurring): string
    {
        $range = getPeriodRange($period);
        $start = $range['start']->format('Y-m-d');
        $end = $range['end']->format('Y-m-d');

        $expensesTotal = (float) Expense::whereBetween('date', [$start, $end])->sum('amount');
        $incomesTotal = (float) Income::whereBetween('date', [$start, $end])->sum('amount');
        $gain = $incomesTotal - $expensesTotal;

        $label = $isCurrent ? 'Période ACTUELLE' : 'Période';
        $s = "$label $period (du {$range['start']->format('d/m/Y')} au {$range['end']->format('d/m/Y')}):\n";
        $s .= "  - Dépenses TOTAL: " . number_format($expensesTotal, 2, ',', ' ') . " $currency\n";
        $s .= "  - Entrées d'argent TOTAL: " . number_format($incomesTotal, 2, ',', ' ') . " $currency\n";
        $s .= "  - GAIN NET de la période (entrées - dépenses): " . number_format($gain, 2, ',', ' ') . " $currency\n";

        // Top 3 catégories (précise que c'est partiel)
        $top = Expense::whereBetween('date', [$start, $end])
            ->select('category_key', DB::raw('SUM(amount) as total'))
            ->groupBy('category_key')
            ->orderByDesc('total')
            ->limit(3)
            ->get();
        if ($top->isNotEmpty()) {
            $s .= "  - Top 3 catégories (partiel, pas le total): ";
            $s .= $top->map(fn ($c) => ($c->category_key ?: 'Autre') . ' ' . number_format((float) $c->total, 0, ',', ' '))
                ->implode(', ') . "\n";
        }

        // Récurrentes VUES dans cette période précise
        $seen = [];
        foreach ($recurring as $desc => $info) {
            if (isset($info['periods'][$period])) {
                $seen[] = "\"{$desc}\" [" . $info['category'] . "] " . number_format($info['periods'][$period], 2, ',', ' ') . " $currency";
            }
        }
        if ($seen) {
            $s .= "  - Dépenses RÉCURRENTES payées DANS cette période: " . implode('; ', $seen) . "\n";
        }

        return rtrim($s);
    }

    /**
     * Détecte les récurrentes : même (description normalisée + catégorie) présente dans >= 3 périodes.
     * Retourne [desc_norm => ['category' => key, 'periods' => [Y-m => montant], 'total' => float]].
     */
    protected function recurringMap(array $periods): array
    {
        if (empty($periods)) {
            return [];
        }
        $periodCount = count($periods);
        if ($periodCount < 3) {
            return [];
        }

        $oldestRange = getPeriodRange(min($periods));
        $newestRange = getPeriodRange(max($periods));

        $rows = Expense::whereBetween('date', [$oldestRange['start']->format('Y-m-d'), $newestRange['end']->format('Y-m-d')])
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->get(['description', 'category_key', 'date', 'amount']);

        // Groupe en PHP (la période comptable du 21->20 n'est pas exprimable en SQL simple)
        $grouped = [];
        foreach ($rows as $r) {
            $desc = mb_strtolower(trim($r->description));
            if ($desc === '') {
                continue;
            }
            $p = getPeriodFromDate($r->date);
            $key = $desc . '|' . ($r->category_key ?: 'Autre');
            $grouped[$key]['desc'] = $desc;
            $grouped[$key]['category'] = $r->category_key ?: 'Autre';
            $grouped[$key]['periods'][$p] = ($grouped[$key]['periods'][$p] ?? 0) + (float) $r->amount;
        }

        // Ne garde que celles présentes dans >= 3 périodes DIFFÉRENTES
        $result = [];
        foreach ($grouped as $g) {
            if (count($g['periods']) >= 3) {
                $result[$g['desc']] = [
                    'category' => $g['category'],
                    'periods'  => $g['periods'],
                    'total'    => array_sum($g['periods']),
                ];
            }
        }
        // Tri par nombre de périodes décroissant, limité à 10
        uasort($result, fn ($a, $b) => count($b['periods']) <=> count($a['periods']));
        return array_slice($result, 0, 10, true);
    }

    protected function recurringSummary(array $recurring, string $currency): string
    {
        if (empty($recurring)) {
            return "Dépenses récurrentes: aucune régularité détectée (>= 3 périodes) sur la couverture de données.";
        }
        $s = "Dépenses RÉCURRENTES (>= 3 périodes différentes) avec leurs PÉRIODES PRÉCISES:";
        foreach ($recurring as $desc => $info) {
            $periodList = [];
            foreach ($info['periods'] as $p => $amount) {
                $periodList[] = "$p (" . number_format($amount, 0, ',', ' ') . " $currency)";
            }
            $s .= "\n  - \"{$desc}\" [" . $info['category'] . "] : " . count($info['periods']) . " périodes → "
                . implode(', ', $periodList)
                . " | total " . number_format($info['total'], 2, ',', ' ') . " $currency";
        }
        return $s;
    }

    protected function previousPeriod(string $period): string
    {
        $d = \Carbon\Carbon::createFromFormat('Y-m', $period)->day(1)->subMonth();
        return $d->format('Y-m');
    }

    protected function periodIsEmpty(string $period): bool
    {
        $range = getPeriodRange($period);
        $start = $range['start']->format('Y-m-d');
        $end = $range['end']->format('Y-m-d');
        $hasExpenses = Expense::whereBetween('date', [$start, $end])->exists();
        $hasIncomes = Income::whereBetween('date', [$start, $end])->exists();
        return !$hasExpenses && !$hasIncomes;
    }
}
