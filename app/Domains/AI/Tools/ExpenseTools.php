<?php

namespace App\Domains\AI\Tools;

use App\Domains\Employees\Models\Employee;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Treasury\Models\Income;
use App\Domains\Treasury\Models\MonthlyClosure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Agrégats sûrs exposés à l'IA (aucun SQL brut, aucune donnée sensible par ligne).
 *
 * PRINCIPE ANTI-HALLUCINATION : chaque domaine de données de la plateforme a SA section
 * explicite dans le contexte. Si un domaine est vide, on l'annonce VIDE — le modèle n'a
 * jamais à deviner ce qui n'est pas listé. L'ordre des sections va du plus demandé
 * (périodes) au plus rare (clôtures), avec un budget de taille maîtrisé (~6-8 Ko).
 *
 * Sections :
 *   1. Résumé exécutif (solde global, chiffres clés en tête = ancrage fort)
 *   2. Périodes comptables (courante + N précédentes, vides incluses explicitement)
 *   3. Dépenses récurrentes avec leurs périodes précises
 *   4. Catégories EXHAUSTIVES (table expense_categories) avec totaux cumulés
 *   5. Employés (effectif, masse salariale théorique, top rôles)
 *   6. Clôtures mensuelles (historique officiel validé)
 *   7. Entrées d'argent (incomes, soft-delete exclus — même règle que l'UI)
 *   8. Rappels de couverture (ce que l'IA ne voit PAS : mots de passe, notes privées...)
 */
class ExpenseTools
{
    /** Nombre de périodes passées incluses dans le contexte (overridable via /settings, defaut 6). */
    protected int $historyPeriods = 6;

    public function __construct()
    {
        // Controle profond : le gerant choisit combien de periodes l IA connait (1-12)
        $this->historyPeriods = \App\Domains\AI\Support\WidgetConfig::get()['historyPeriods'];
    }

    public function buildContext(): string
    {
        // CACHE 60s : ce contexte fait ~40 requetes SQL. Les donnees changent rarement
        // en 1 minute ; le cache divise la charge quand le gérant enchaîne les questions.
        // La LOCALE fait partie de la cle : les noms de categories (FR/AR/EN) sont rendus
        // dans la langue du user — sinon un switch de langue servait l'ancien contexte
        // cache pendant 60 s.
        return Cache::remember('ai_expense_context:' . getCurrency() . ':' . app()->getLocale(), 60, function () {
            return $this->buildContextRaw();
        });
    }

    protected function buildContextRaw(): string
    {
        $currency = getCurrency();
        $currentPeriod = getPeriodFromDate(now());

        // Les périodes couvertes : courante + N précédentes (les VIDES aussi, marquées
        // explicitement "AUCUNE donnée" — sinon l'IA déduisait à tort sur une période skippée)
        $periods = [$currentPeriod];
        $period = $currentPeriod;
        for ($i = 0; $i < $this->historyPeriods; $i++) {
            $period = $this->previousPeriod($period);
            $periods[] = $period;
        }

        // Carte des dépenses récurrentes : desc_norm => [périodes précises + montants]
        $recurring = $this->recurringMap($periods);

        $lines = [];

        // ---- 1. RESUME EXECUTIF (ancrage fort : les 3 chiffres les plus demandes) ----
        $lines[] = $this->executiveSummary($currentPeriod, $currency);

        // ---- 2. PERIODES ----
        foreach ($periods as $p) {
            if ($this->periodIsEmpty($p)) {
                // Periode vide EXPLICITE : l IA doit dire "aucune donnee" au lieu de deduire
                $range = getPeriodRange($p);
                $lines[] = "Période {$p} (du {$range['start']->format('d/m/Y')} au {$range['end']->format('d/m/Y')}): AUCUNE donnée enregistrée (ni dépense ni entrée). Si on te demande cette période, réponds qu'elle est vide.";
                continue;
            }
            $lines[] = $this->periodSummary($p, $currency, $p === $currentPeriod, $recurring);
        }

        // ---- 3. RECURRENTES ----
        $lines[] = $this->recurringSummary($recurring, $currency);

        // ---- 4. CATEGORIES EXHAUSTIVES ----
        $lines[] = $this->categoriesSummary();

        // ---- 5. EMPLOYES / MASSES SALARIALES ----
        $lines[] = $this->employeesSummary($currency);

        // ---- 6. CLOTURES MENSUELLES (historique officiel) ----
        $lines[] = $this->closuresSummary($currency);

        // ---- 7. ENTREES D'ARGENT (detail source par source, periode courante) ----
        $lines[] = $this->incomesSummary($periods, $currency);

        // ---- 8. RAPPEL DE COUVERTURE ----
        $lines[] = "COUVERTURE DE TES CONNAISSANCES : tu connais UNIQUEMENT ce qui précède. "
            . "Ce que tu ne vois PAS : notes privées des dépenses, données supprimées (corbeille), "
            . "mots de passe, données d'autres entreprises, périodes plus anciennes que celles listées. "
            . "Pour toute question hors couverture : « cette précision n'est pas dans mes données ».";

        return implode("\n\n", $lines);
    }

    /**
     * Résumé exécutif : les chiffres les plus demandés en tête de contexte.
     * Placer les totaux globaux EN PREMIER ancre fortement le modèle (les premiers
     * tokens du contexte pèsent le plus dans la génération).
     */
    protected function executiveSummary(string $currentPeriod, string $currency): string
    {
        $range = getPeriodRange($currentPeriod);
        $start = $range['start']->format('Y-m-d');
        $end = $range['end']->format('Y-m-d');

        $expTotal = (float) Expense::whereBetween('date', [$start, $end])->sum('amount');
        $incTotal = (float) Income::whereBetween('date', [$start, $end])->sum('amount');

        // Totaux "depuis toujours" (toutes periodes confondues)
        $expAll = (float) Expense::sum('amount');
        $incAll = (float) Income::sum('amount');
        $nbExp = Expense::count();
        $nbInc = Income::count();

        $s = "RÉSUMÉ EXÉCUTIF (chiffres officiels recalculés à l'instant) :\n";
        $s .= "  - Dépenses période actuelle ({$currentPeriod}) : " . number_format($expTotal, 2, ',', ' ') . " $currency\n";
        $s .= "  - Entrées période actuelle : " . number_format($incTotal, 2, ',', ' ') . " $currency\n";
        $s .= "  - Dépenses TOUTES PÉRIODES confondues : " . number_format($expAll, 2, ',', ' ') . " $currency (sur {$nbExp} dépenses enregistrées)\n";
        $s .= "  - Entrées TOUTES PÉRIODES confondues : " . number_format($incAll, 2, ',', ' ') . " $currency (sur {$nbInc} entrées enregistrées)\n";
        return $s;
    }

    /**
     * Employés : effectif, masse salariale théorique, répartition des rôles.
     * Ne liste PAS les noms (donnée personnelle inutile à l'assistant financier,
     * sauf question directe sur un employé — auquel cas les prénoms sont utiles).
     */
    protected function employeesSummary(string $currency): string
    {
        $employees = Employee::whereNull('deleted_at')->get(['name', 'role_title', 'base_salary', 'status']);
        if ($employees->isEmpty()) {
            return "EMPLOYÉS : aucun employé enregistré dans la plateforme.";
        }
        $mass = (float) $employees->sum('base_salary');
        $byRole = $employees->groupBy('role_title')
            ->map(fn ($g) => $g->count() . '× ' . trim($g->first()->role_title ?: 'Sans rôle'));
        $active = $employees->where('status', 'active')->count();

        $s = "EMPLOYES (effectif réel de la plateforme) :\n";
        $s .= "  - " . $employees->count() . " employés dont {$active} actifs\n";
        $s .= "  - Masse salariale MENSUELLE THÉORIQUE (somme des salaires de base) : " . number_format($mass, 2, ',', ' ') . " $currency\n";
        $s .= "  - Répartition : " . $byRole->implode(', ') . "\n";
        $s .= "  - Détail (nom — rôle — salaire de base) : ";
        $s .= $employees->sortBy('name')
            ->map(fn ($e) => $e->name . ' (' . trim($e->role_title ?: '—') . ', ' . number_format((float) $e->base_salary, 0, ',', ' ') . ')')
            ->implode(' ; ');
        return $s;
    }

    /**
     * Clôtures mensuelles : l'historique OFFICIEL validé par le gérant (table monthly_closures).
     * Différent des totaux calculés : ce sont les chiffres figés au moment de la clôture.
     */
    protected function closuresSummary(string $currency): string
    {
        $closures = MonthlyClosure::orderByDesc('month')->get(['month', 'gains', 'expenses', 'balance']);
        if ($closures->isEmpty()) {
            return "CLÔTURES MENSUELLES : aucune clôture validée enregistrée.";
        }
        $s = "CLÔTURES MENSUELLES OFFICIELLES (chiffres figés validés par le gérant) :\n";
        foreach ($closures as $c) {
            $s .= "  - {$c->month} : dépenses " . number_format((float) $c->expenses, 2, ',', ' ')
                . ", gains " . number_format((float) $c->gains, 2, ',', ' ')
                . ", solde " . number_format((float) $c->balance, 2, ',', ' ') . " $currency\n";
        }
        return rtrim($s);
    }

    /**
     * Entrées d'argent : détail source par source des périodes couvertes.
     * Les incomes soft-deleted sont EXCLUS (même règle que l'interface — le gérant
     * voit 0 entrée si toutes sont en corbeille, l'IA doit voir pareil).
     */
    protected function incomesSummary(array $periods, string $currency): string
    {
        $oldest = getPeriodRange(min($periods));
        $newest = getPeriodRange(max($periods));

        $rows = Income::whereBetween('date', [$oldest['start']->format('Y-m-d'), $newest['end']->format('Y-m-d')])
            ->orderBy('date')
            ->get(['date', 'amount', 'source_type', 'sub_type', 'source_name']);

        if ($rows->isEmpty()) {
            return "ENTRÉES D'ARGENT : AUCUNE entrée active enregistrée sur les périodes couvertes (des entrées peuvent exister dans la corbeille — elles ne comptent pas).";
        }

        $s = "ENTRÉES D'ARGENT (détail exhaustif des entrées ACTIVES sur la couverture) :\n";
        foreach ($rows as $r) {
            $s .= "  - {$r->date->format('d/m/Y')} : " . number_format((float) $r->amount, 2, ',', ' ')
                . " $currency [{$r->source_type}" . ($r->sub_type ? "/{$r->sub_type}" : "") . "]"
                . ($r->source_name ? " {$r->source_name}" : "") . "\n";
        }
        return rtrim($s);
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

        // TOUTES les catégories de la période (pas juste top 3) : couvre "combien en X ?"
        $byCat = Expense::whereBetween('date', [$start, $end])
            ->select('category_key', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as n'))
            ->groupBy('category_key')
            ->orderByDesc('total')
            ->get();
        if ($byCat->isNotEmpty()) {
            $s .= "  - Détail COMPLET par catégorie (catégorie: total sur N dépenses): ";
            $s .= $byCat->map(fn ($c) => ($c->category_key ?: 'other') . ': ' . number_format((float) $c->total, 0, ',', ' ') . " ({$c->n})")
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

    /**
     * Liste EXHAUSTIVE des catégories définies (table expense_categories) avec leur
     * total cumulé. C'est la source de vérité pour toute question "quelles catégories".
     */
    protected function categoriesSummary(): string
    {
        if (!Schema::hasTable('expense_categories')) {
            return '';
        }
        $cats = DB::table('expense_categories')->orderBy('id')->get(['key', 'name_fr', 'name_ar', 'name_en', 'is_active', 'parent_id']);
        if ($cats->isEmpty()) {
            return '';
        }
        // Totaux cumules par categorie (toutes periodes confondues) pour donner du contexte
        $totals = Expense::select('category_key', DB::raw('SUM(amount) as total'))
            ->groupBy('category_key')
            ->pluck('total', 'category_key');

        $locale = app()->getLocale();
        // Le contexte donne le nom dans la LANGUE DU USER en premier : l'IA doit PRESENTER
        // les categories sous ce nom (pas la cle technique). FR -> "Salaires", AR -> "الرواتب".
        $lines = ["Catégories de dépenses DÉFINIES dans la plateforme (liste EXHAUSTIVE — n'en invente aucune autre) :"];
        foreach ($cats as $c) {
            $localizedName = match ($locale) {
                'ar' => $c->name_ar ?: $c->name_fr,
                'en' => $c->name_en ?: $c->name_fr,
                default => $c->name_fr ?: $c->key,
            };
            $total = isset($totals[$c->key]) ? number_format((float) $totals[$c->key], 0, ',', ' ') : '0';
            $active = $c->is_active ? '' : ' [INACTIVE]';
            // Format : nom traduit en PREMIER (c'est lui qu'on presente), key technique entre
            // parentheses (reference interne si le user la cherche dans l'UI).
            $lines[] = "  - « {$localizedName} » (id: {$c->key}) — total cumulé {$total}{$active}";
        }
        $usedCount = $totals->count();
        $lines[] = "Il y a exactement " . $cats->count() . " catégories définies, dont {$usedCount} ont des dépenses enregistrées. "
            . "Quand tu listes ou décris ces catégories, utilise TOUJOURS leur nom « {$localizedName} » ci-dessus dans la langue de l'utilisateur — JAMAIS leur identifiant technique (id) seul, sauf si l'utilisateur le demande explicitement. Ne liste JAMAIS d'autres catégories que celles-ci.";
        return implode("\n", $lines);
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
