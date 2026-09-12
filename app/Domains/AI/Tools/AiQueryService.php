<?php

namespace App\Domains\AI\Tools;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Treasury\Models\Income;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Tools internes exposés à Gemini via function calling.
 *
 * PRINCIPES :
 *  - Aucun SQL libre : chaque tool a un contrat JSON strict (paramètres validés).
 *  - Réponses AGRÉGÉES ou bornées (jamais de dump ligne par ligne illimité).
 *  - Cache 60s par tool+args (le gérant enchaîne les questions, la base ne bouge pas).
 *  - Renvoie du TEXTE structuré, pas du JSON brut.
 */
class AiQueryService
{
    protected function cached(string $key, callable $compute): string
    {
        $value = Cache::get($key);
        if (is_string($value) && $value !== '') {
            return $value;
        }
        $value = $compute();
        Cache::put($key, $value, 60);
        return $value;
    }

    /** Déclarations des tools pour Gemini (format functionDeclarations). */
    public function declarations(): array
    {
        return [
            [
                'name' => 'expenses_by_day',
                'description' => "Total des dépenses pour UN jour précis (YYYY-MM-DD). Répond aussi à « dépenses d'aujourd'hui », « d'hier », « le 27 août ». Avec category optionnel, croise jour × catégorie.",
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'date' => ['type' => 'STRING', 'description' => 'Date YYYY-MM-DD'],
                        'category' => ['type' => 'STRING', 'description' => 'Clé de catégorie optionnelle (other, salaries, rent…)'],
                    ],
                    'required' => ['date'],
                ],
            ],
            [
                'name' => 'expenses_range',
                'description' => "Totaux de dépenses sur une PLAGE (from/to YYYY-MM-DD), répartis par catégorie. Permet le mois calendaire 1-30 (from=1er du mois, to=dernier jour) ou une semaine.",
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'from' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                        'to' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                        'category' => ['type' => 'STRING', 'description' => 'Filtre catégorie optionnel'],
                    ],
                    'required' => ['from', 'to'],
                ],
            ],
            [
                'name' => 'recent_expenses',
                'description' => "Les N dernières dépenses (max 10) : id, date, description, montant, catégorie, moyen de paiement. Utiliser quand le gérant veut le détail d'une saisie récente.",
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'limit' => ['type' => 'INTEGER', 'description' => '1-10, défaut 5'],
                    ],
                ],
            ],
            [
                'name' => 'incomes_range',
                'description' => "Entrées d'argent sur une plage (YYYY-MM-DD), détail lignes + total.",
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'from' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                        'to' => ['type' => 'STRING', 'description' => 'YYYY-MM-DD'],
                    ],
                    'required' => ['from', 'to'],
                ],
            ],
        ];
    }

    /** Dispatcher : $name validé contre la liste des déclarations. */
    public function call(string $name, array $args): string
    {
        return match ($name) {
            'expenses_by_day' => $this->expensesByDay($args),
            'expenses_range' => $this->expensesRange($args),
            'recent_expenses' => $this->recentExpenses($args),
            'incomes_range' => $this->incomesRange($args),
            default => "Outil {$name} inconnu — réponds sans tool.",
        };
    }

    protected function safeDate(?string $v, ?string $fallback = null): ?string
    {
        if (!$v) return $fallback;
        $d = \Carbon\Carbon::createFromFormat('Y-m-d', $v);
        return $d ? $d->format('Y-m-d') : $fallback;
    }

    protected function fmt(float $v): string
    {
        return number_format($v, 2, ',', ' ').' '.getCurrency();
    }

    protected function expensesByDay(array $args): string
    {
        $date = $this->safeDate($args['date'] ?? null, now()->format('Y-m-d'));
        if (!$date) return "Paramètre date invalide.";
        $currency = getCurrency();

        return $this->cached("ai_tool:ebd:{$date}:".($args['category'] ?? '').':'.$currency, function () use ($date, $args, $currency) {
            $q = Expense::whereDate('date', $date);
            if (!empty($args['category'])) {
                $q->where('category_key', $args['category']);
            }
            $total = (float) $q->sum('amount');
            $count = $q->count();

            $out = "Résultat expenses_by_day pour le {$date} :\n";
            $out .= "- Total : ".number_format($total, 2, ',', ' ')." $currency sur {$count} dépense(s)\n";
            if ($count === 0) {
                return $out."- AUCUNE dépense enregistrée ce jour\n";
            }
            if (empty($args['category'])) {
                $rows = Expense::whereDate('date', $date)
                    ->selectRaw('category_key, SUM(amount) as s, COUNT(*) as n')
                    ->groupBy('category_key')
                    ->get();
                $out .= "- Répartition : ".$rows->map(fn ($r) => ($r->category_key ?: 'other').': '.number_format((float) $r->s, 2, ',', ' ').' ('.$r->n.')')->implode('; ')."\n";
            }
            // Borné : max 5 montants bruts par jour, jamais de dump complet
            $sample = Expense::whereDate('date', $date)->orderByDesc('amount')->limit(5)->get(['description', 'amount']);
            $out .= "- Top ".$sample->count()." : ".$sample->map(fn ($e) => $e->description.' ('.number_format((float) $e->amount, 0, ',', ' ').')')->implode('; ');
            return $out;
        });
    }

    protected function expensesRange(array $args): string
    {
        $from = $this->safeDate($args['from'] ?? null, now()->startOfMonth()->format('Y-m-d'));
        $to = $this->safeDate($args['to'] ?? null, now()->endOfMonth()->format('Y-m-d'));
        if (!$from || !$to || $from > $to) return "Paramètres from/to invalides.";
        $currency = getCurrency();

        return $this->cached("ai_tool:er:{$from}:{$to}:".($args['category'] ?? '').':'.$currency, function () use ($from, $to, $args, $currency) {
            $q = Expense::whereBetween('date', [$from, $to]);
            if (!empty($args['category'])) {
                $q->where('category_key', $args['category']);
            }
            $total = (float) $q->sum('amount');
            $count = $q->count();

            $s = "Résultat expenses_range du {$from} au {$to} :\n";
            if (!empty($args['category'])) {
                $s .= "(filtre catégorie = {$args['category']})\n";
            }
            $s .= "- Total global : ".number_format($total, 2, ',', ' ')." $currency sur {$count} dépense(s)\n";
            if ($count === 0) {
                return $s."- AUCUNE dépense dans cette plage\n";
            }
            $byCat = Expense::whereBetween('date', [$from, $to])
                ->select('category_key', DB::raw('SUM(amount) as s'), DB::raw('COUNT(*) as n'))
                ->groupBy('category_key')->orderByDesc('s')->get();
            foreach ($byCat as $r) {
                $s .= "  · ".($r->category_key ?: 'other')." : ".number_format((float) $r->s, 2, ',', ' ')." $currency ({$r->n} opérations)\n";
            }
            return $s;
        });
    }

    protected function recentExpenses(array $args): string
    {
        $limit = min(10, max(1, (int) ($args['limit'] ?? 5)));
        $currency = getCurrency();
        $rows = Expense::orderByDesc('date')->orderByDesc('id')->limit($limit)
            ->get(['id', 'date', 'description', 'amount', 'category_key', 'payment_method']);
        if ($rows->isEmpty()) {
            return "Résultat recent_expenses : aucune dépense enregistrée.";
        }
        $s = "Résultat recent_expenses (les {$limit} dernières, sans notes privées) :\n";
        foreach ($rows as $e) {
            $s .= "- id={$e->id}, ".\Carbon\Carbon::parse($e->date)->format('d/m/Y')." : ".number_format((float) $e->amount, 2, ',', ' ')." $currency — {$e->description} [".($e->category_key ?: 'other')."] ".($e->payment_method ?: '-')."\n";
        }
        return $s;
    }

    protected function incomesRange(array $args): string
    {
        $from = $this->safeDate($args['from'] ?? null, now()->startOfMonth()->format('Y-m-d'));
        $to = $this->safeDate($args['to'] ?? null, now()->endOfMonth()->format('Y-m-d'));
        $currency = getCurrency();
        $rows = Income::whereBetween('date', [$from, $to])->orderBy('date')
            ->get(['date', 'amount', 'source_type', 'source_name']);
        if ($rows->isEmpty()) {
            return "Résultat incomes_range du {$from} au {$to} : AUCUNE entrée d'argent enregistrée.";
        }
        $s = "Résultat incomes_range du {$from} au {$to} :\n";
        foreach ($rows as $r) {
            $s .= "  - ".\Carbon\Carbon::parse($r->date)->format('d/m/Y')." : ".number_format((float) $r->amount, 2, ',', ' ')." $currency [{$r->source_type}]".($r->source_name ? " {$r->source_name}" : '')."\n";
        }
        $s .= "  - Total : ".number_format((float) $rows->sum('amount'), 2, ',', ' ')." $currency";
        return $s;
    }
}
