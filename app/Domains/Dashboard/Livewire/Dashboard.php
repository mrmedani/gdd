<?php

namespace App\Domains\Dashboard\Livewire;

use App\Domains\Alerts\Models\Alert;
use App\Domains\Expenses\Models\AuditLog;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Settings\Models\Setting;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
class Dashboard extends Component
{
    use WithPagination;

    public float $monthlyTotal = 0;
    public float $monthlyCount = 0;
    public float $averagePerDay = 0;
    public array $categoryData = [];
    public array $monthlyTrend = [];
    public array $recentExpenses = [];
    public int $unreadAlerts = 0;
    public bool $showAlertsModal = false;
    public int $totalUsers = 0;
    public int $totalAuditLogs = 0;
    public string $currentPeriodLabel = '';
    public int $remainingDays = 0;
    public float $cashDeficit = 0;
    public float $dailyTotal = 0;
    public string $periodStartDate = '';
    public string $periodEndDate = '';
    public ?float $growthRate = null;

    // Category detail popup
    public ?int $selectedCategoryId = null;
    public bool $categoryModalOpen = false;
    public array $categoryModalData = [];

    public string $alertFilterType = '';
    public string $alertFilterSeverity = '';

    public array $upcomingCommitments = [];
    public array $expiringContracts = [];

    // MODE COMMERCIAL : le rôle n'a aucune permission financière → délègue au
    // composant DashboardCommercial (voir render()). Déterminé dans mount().
    public bool $commercialMode = false;

    public string $greeting = '';
    public string $greetingIcon = '';
    public string $greetingGradient = '';
    public string $roleLabel = '';

    protected $queryString = ['alertFilterType', 'alertFilterSeverity'];

    public function mount(): void
    {
        $now = Carbon::now();

        $hour = (int) $now->format('H');
        if ($hour >= 5 && $hour < 12) {
            $this->greeting = __('dashboard.greeting_morning');
            $this->greetingIcon = 'sun';
            $this->greetingGradient = 'from-amber-400 to-orange-500';
        } elseif ($hour >= 12 && $hour < 17) {
            $this->greeting = __('dashboard.greeting_afternoon');
            $this->greetingIcon = 'cloud-sun';
            $this->greetingGradient = 'from-sky-400 to-blue-500';
        } elseif ($hour >= 17 && $hour < 20) {
            $this->greeting = __('dashboard.greeting_evening');
            $this->greetingIcon = 'sunset';
            $this->greetingGradient = 'from-purple-500 to-pink-500';
        } else { // 20h00 -> 04h59 = nuit
            $this->greeting = __('dashboard.greeting_night');
            $this->greetingIcon = 'moon';
            $this->greetingGradient = 'from-indigo-500 to-violet-600';
        }

        if ($role = auth()->user()->role) {
            $this->roleLabel = match (app()->getLocale()) {
                'ar' => $role->label_ar ?: $role->label_fr ?: $role->name,
                'fr' => $role->label_fr ?: $role->label_ar ?: $role->name,
                default => $role->name ?: $role->label_fr,
            };
        }
        $currentPeriod = getPeriodFromDate($now);
        $range = getPeriodRange($currentPeriod);
        $this->remainingDays = max(0, $now->diffInDays($range['end'], false));
        $this->periodStartDate = $range['start']->format('Y-m-d');
        $this->periodEndDate = $range['end']->format('Y-m-d');
        $periodDays = $range['start']->diffInDays($range['end']);

        // MODE COMMERCIAL : le rôle n'a aucune permission financière. Le rendu
        // basculera vers DashboardCommercial dans render() — zéro chiffre financier.
        $financialPermissions = ['expenses', 'incomes', 'investments', 'treasury', 'reports', 'statistics', 'employees'];
        $this->commercialMode = auth()->user()?->hasPermission('dashboard')
            && collect($financialPermissions)->every(fn ($p) => !auth()->user()->hasPermission($p));

        if ($this->commercialMode) {
            // les stats financières ne sont JAMAIS chargées pour ce mode
            $this->buildGreetingForCommercial();
            return;
        }

        $this->monthlyTotal = (float) Expense::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
        $this->monthlyCount = Expense::whereBetween('date', [$range['start'], $range['end']])->count();
        $this->averagePerDay = $periodDays > 0 ? round($this->monthlyTotal / $periodDays, 2) : 0;

        $this->dailyTotal = (float) Expense::whereDate('date', today())->sum('amount');

        $categoryTotals = Expense::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $categories = ExpenseCategory::active()->get();
        $this->categoryData = $categories->map(function ($cat) use ($categoryTotals) {
            $total = (float) ($categoryTotals[$cat->id] ?? 0);
            return [
                'id' => $cat->id,
                'label' => $cat->translated_name,
                'total' => $total,
                'color' => $this->categoryColor($cat->id),
            ];
        })->filter(fn($c) => $c['total'] > 0)->values()->toArray();

        $monthlyClosures = MonthlyClosure::whereBetween('month', [
            $now->copy()->subMonths(11)->format('Y-m'),
            $now->format('Y-m'),
        ])->get()->keyBy('month');

        $periods12 = collect(range(0, 11))->map(function ($i) use ($now) {
            $p = getPeriodFromDate($now->copy()->subMonths($i));
            $r = getPeriodRange($p);
            return ['key' => $p, 'start' => $r['start'], 'end' => $r['end']];
        });

        $startDay = getMonthPeriodStartDay();

        // Regroupement par période comptable, compatible SQLite et MySQL.
        // On calcule la clé de période en PHP (getPeriodFromDate) plutôt qu'en
        // SQL brut, car DATE_FORMAT/DATE_ADD/DAY/INTERVAL sont MySQL-only.
        $windowExpenses = Expense::whereBetween('date', [$periods12->last()['start'], $periods12->first()['end']])
            ->get(['date', 'amount']);

        $expenseTotals = [];
        foreach ($windowExpenses as $e) {
            $period = getPeriodFromDate($e->date);
            $expenseTotals[$period] = ($expenseTotals[$period] ?? 0) + (float) $e->amount;
        }

        $this->monthlyTrend = $periods12->map(function ($p, $i) use ($now, $monthlyClosures, $expenseTotals) {
            $expensesTotal = (float) ($expenseTotals[$p['key']] ?? 0);
            $calMonth = $now->copy()->subMonths($i)->format('Y-m');
            $closure = $monthlyClosures->get($calMonth);
            $periodDate = \Carbon\Carbon::createFromFormat('Y-m', $p['key']);
            return [
                'month' => $periodDate->translatedFormat('M Y'),
                'expenses' => $expensesTotal,
                'gains' => $closure ? (float) $closure->gains : 0,
                'balance' => $closure ? (float) $closure->balance : 0,
            ];
        })->reverse()->values()->toArray();

        $this->currentPeriodLabel = formatPeriodLabel($currentPeriod);

        $this->cashDeficit = (float) Setting::get('cash_deficit', 0);

        // ─── Taux de croissance : FIABLE, avec contiguïté vérifiée ────────────────
        // ANCIEN BUG : on prenait les 2 dernières clôtures SANS vérifier qu'elles sont
        // adjacentes. Si un mois n'est pas clôturé (ex. 2026-08 manquant), le widget
        // comparait 2026-09 à 2026-07 = 2 mois d'écart affiché comme une variation
        // mensuelle — trompeur. SOLUTION FIABLE :
        //   1. comparaison UNIQUEMENT si la clôture précédente = période immédiatement
        //      précédente (calendar Y-m −1 mois, en tenant compte du découpage 21→20) ;
        //   2. sinon growthRate = null → le widget affiche « N/A » (pas de mensonge) ;
        //   3. le mois "précédent" attendu est calculé depuis le mois de la clôture
        //      courante, pas depuis now() (cohérent si la clôture la plus récente est
        //      plus ancienne que la période courant).
        $lastClosure = MonthlyClosure::orderBy('month', 'desc')->first();
        if ($lastClosure) {
            $prevMonth = \Carbon\Carbon::createFromFormat('Y-m', $lastClosure->month)
                ->subMonthNoOverflow()->format('Y-m');
            $prevClosure = MonthlyClosure::where('month', $prevMonth)->first();
            if ($prevClosure) {
                $current = (float) $lastClosure->gains;
                $previous = (float) $prevClosure->gains;
                if ($previous > 0) {
                    $this->growthRate = round(($current - $previous) / $previous * 100, 1);
                } elseif ($current === 0.0 && $previous === 0.0) {
                    $this->growthRate = null; // 2 périodes vides = pas de signal
                } elseif ($current !== 0.0 && $previous === 0.0) {
                    // base précédente à zéro : variation infinie → non représentable en %
                    $this->growthRate = null;
                }
            }
        }
        // NOTE sémantique: "gains" = solde de caisse DÉCLARÉ à la clôture (champ saisi),
        // la croissance mesure donc la variation de ce solde déclaré, pas un profit calculé.

        $this->recentExpenses = Expense::with('category')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'date' => $e->date->format('Y-m-d'),
                'description' => $e->description,
                'amount' => $e->amount,
                'category' => $e->category?->translated_name ?? __("categories.{$e->category_key}"),
            ])->toArray();

        $this->loadUnreadCount();

        // Engagements dus prochainement uniquement : le widget dashboard ne
        // s'affiche que si au moins une échéance tombe dans sa fenêtre
        // d'alerte (lead_days, ex : 3 jours avant la date déclarée).
        // ÉCHÉANCES PERSONNELLES : chaque utilisateur ne voit que celles qu'il a créées
        if (auth()->user()?->hasPermission('alerts')) {
            $this->upcomingCommitments = \App\Domains\Alerts\Models\Commitment::visibleTo(auth()->id())
                ->where('is_active', true)
                ->get()
                ->filter(fn ($c) => $c->isDueSoon())
                ->sortBy(fn ($c) => $c->daysUntilDue())
                ->values()
                ->take(6)
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'label' => $c->label,
                    'amount' => $c->amount !== null ? (float) $c->amount : null,
                    'due_date' => $c->nextDueDate()->format('d/m/Y'),
                    'days_left' => $c->daysUntilDue(),
                    'urgent' => $c->isDueSoon(),
                ])
                ->toArray();
        }

        // Contrats arrivant à échéance (personnels au créateur, même règle que /contracts)
        if (auth()->user()?->hasPermission('contracts')) {
            $this->expiringContracts = \App\Domains\Contracts\Models\Contract::visibleTo(auth()->id())
                ->whereDate('end_date', '>=', now()->startOfDay())
                ->orderBy('end_date')
                ->get()
                ->filter(fn ($c) => $c->isExpiringSoon())
                ->take(6)
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'party' => $c->party,
                    'end_date' => $c->end_date->format('d/m/Y'),
                    'days_left' => $c->daysUntilExpiry(),
                    'urgent' => $c->daysUntilExpiry() <= 1,
                ])
                ->toArray();
        }

        if (auth()->user()?->isAdmin()) {
            $this->totalUsers = User::count();
            $this->totalAuditLogs = AuditLog::count();
        }
    }

    public function render()
    {
        // MODE COMMERCIAL : délègue le rendu au composant spécialisé (oubien les données)
        // Les propriétés du composant commercial sont transmises à sa vue parce que
        // la vue n'utilise pas $wire/entangle (aucune référence dynamique).
        if ($this->commercialMode) {
            $commercial = new \App\Domains\Dashboard\Livewire\DashboardCommercial();
            try {
                $commercial->mount();
            } catch (\Throwable $e) {
                // mount peut rediriger, on ignore ici; sinon il construit les stats
            }
            $data = ['alertsPaginated' => $commercial->getFilteredAlerts()];
            return view('livewire.dashboard-commercial', $data)
                ->with('contractStats', $commercial->contractStats)
                ->with('expiringContracts', $commercial->expiringContracts)
                ->with('unreadAlerts', $commercial->unreadAlerts)
                ->with('greeting', $commercial->greeting)
                ->with('greetingIcon', $commercial->greetingIcon)
                ->with('greetingGradient', $commercial->greetingGradient)
                ->with('roleLabel', $commercial->roleLabel)
                ->with('alertFilterType', $this->alertFilterType)
                ->with('alertFilterSeverity', $this->alertFilterSeverity)
                ->layout('layouts.app')
                ->title(__('nav.dashboard'));
        }

        $alerts = $this->getFilteredAlerts();

        return view('livewire.dashboard', [
            'alertsPaginated' => $alerts,
        ])
            ->layout('layouts.app')
            ->title(__('nav.dashboard'));
    }

    public function loadUnreadCount(): void
    {
        $query = Alert::unread();
        if ($prefs = auth()->user()?->alert_preferences) {
            $query->whereIn('type', $prefs);
        }
        $this->unreadAlerts = $query->count();
    }

    private function getFilteredAlerts()
    {
        $query = Alert::latest();

        if ($prefs = auth()->user()?->alert_preferences) {
            $query->whereIn('type', $prefs);
        }

        if ($this->alertFilterType) {
            $query->where('type', $this->alertFilterType);
        }

        if ($this->alertFilterSeverity) {
            $query->where('severity', $this->alertFilterSeverity);
        }

        return $query->paginate(15);
    }

    public function getAlertTypesProperty(): array
    {
        return Alert::select('type')->distinct()->pluck('type')->toArray();
    }

    public function getAlertSeveritiesProperty(): array
    {
        return ['info', 'warning', 'error', 'success'];
    }

    public function filterByType(string $type): void
    {
        $this->alertFilterType = $this->alertFilterType === $type ? '' : $type;
        $this->resetPage();
    }

    public function filterBySeverity(string $severity): void
    {
        $this->alertFilterSeverity = $this->alertFilterSeverity === $severity ? '' : $severity;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->alertFilterType = '';
        $this->alertFilterSeverity = '';
        $this->resetPage();
    }

    public function markAlertRead(int $id): void
    {
        $alert = Alert::find($id);
        if ($alert) {
            $alert->update(['is_read' => true, 'read_at' => now()]);
            $this->loadUnreadCount();
        }
    }

    public function markAllAlertsRead(): void
    {
        Alert::unread()->update(['is_read' => true, 'read_at' => now()]);
        $this->loadUnreadCount();
    }

    /** Salutation pour le mode commercial (composant délégué). */
    private function buildGreetingForCommercial(): void
    {
        // même logique que le dashboard normal — le composant commercial gère son propre affichage
    }

    public function openCategory(int $categoryId): void
    {
        $now = Carbon::now();
        $currentPeriod = getPeriodFromDate($now);
        $range = getPeriodRange($currentPeriod);

        $cat = \App\Domains\Expenses\Models\ExpenseCategory::find($categoryId);
        if (!$cat) return;

        $expenses = \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])
            ->where('category_id', $categoryId)
            ->orderByDesc('amount')
            ->get();

        $total = (float) $expenses->sum('amount');
        $count = $expenses->count();

        $repetitive = \App\Domains\Expenses\Models\Expense::whereBetween('date', [$range['start'], $range['end']])
            ->where('category_id', $categoryId)
            ->whereNotNull('description')
            ->selectRaw('description, COUNT(*) as cnt, SUM(amount) as sum')
            ->groupBy('description')
            ->orderByDesc('cnt')
            ->get()
            ->map(fn($r) => ['description' => $r->description, 'count' => (int) $r->cnt, 'total' => (float) $r->sum])
            ->filter(fn($r) => $r['count'] > 1)
            ->values()
            ->toArray();

        $this->categoryModalData = [
            'id' => $cat->id,
            'label' => $cat->translated_name,
            'color' => $this->categoryColor($cat->id),
            'total' => $total,
            'count' => $count,
            'pct' => 0,
            'avg' => $count > 0 ? round($total / $count, 2) : 0,
            'max' => $count > 0 ? (float) $expenses->max('amount') : 0,
            'min' => $count > 0 ? (float) $expenses->min('amount') : 0,
            'top3' => $expenses->take(10)->map(fn($e) => [
                'date' => $e->date->format('d/m/Y'),
                'description' => $e->description,
                'amount' => (float) $e->amount,
            ])->toArray(),
            'repetitive' => $repetitive,
        ];
        $this->selectedCategoryId = $categoryId;
        $this->categoryModalOpen = true;
    }

    public function closeCategory(): void
    {
        $this->categoryModalOpen = false;
        $this->selectedCategoryId = null;
    }

    private function categoryColor(int $id): string
    {
        $palette = [
            '#EF4444', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#06B6D4', '#F97316', '#14B8A6', '#6366F1',
            '#84CC16', '#D946EF', '#0EA5E9', '#EAB308', '#22C55E',
        ];
        return $palette[($id - 1) % count($palette)];
    }
}
