<?php

namespace App\Domains\Reports\Livewire;

use App\Domains\Employees\Models\Employee;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Expenses\Models\ExpenseCategory;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Shared\Enums\PaymentMethod;
use Illuminate\Support\Facades\Gate;
use App\Shared\Livewire\WithToast;
use Livewire\Component;

class ReportsIndex extends Component
{
    use WithToast;
    public string $selectedPeriod = '';
    public int $annualYear;
    public ?int $filterCategory = null;
    public ?int $filterEmployee = null;
    public string $filterPaymentMethod = '';

    // Rapport personnalisé (plage de dates libre)
    public string $rangeFrom = '';
    public string $rangeTo = '';

    // Preview annuel (FIX #7)
    public float $annualPreviewTotal = 0;
    public int $annualPreviewCount = 0;
    public float $annualPreviewAvg = 0;
    public array $annualPreviewByMonth = [];
    public array $annualPreviewByCategory = [];
    public ?int $annualCategory = null;
    public ?int $annualEmployee = null;
    public string $annualPaymentMethod = '';

    public float $previewTotal = 0;
    public int $previewCount = 0;
    public float $previewGains = 0;
    public float $previewBalance = 0;
    public string $previewPeriodLabel = '';
    public array $previewByCategory = [];
    public string $previewPeriodStart = '';
    public string $previewPeriodEnd = '';

    public function mount(): void
    {
        Gate::authorize('manage-reports');
        $this->selectedPeriod = getPeriodFromDate(now());
        $this->annualYear = (int) now()->year;
        $this->refreshPreview();
        $this->refreshAnnualPreview();
    }

    /**
     * FIX #1 : le preview suit TOUS les changements de filtres (avant, les filtres
     * cat/employé/paiement étaient ignorés dans le preview mais appliqués au PDF
     * téléchargé → le preview « mentait »).
     * FIX #5 : annualYear met à jour le preview ANNUEL, plus le mensuel.
     */
    public function updated(string $property, mixed $value): void
    {
        if ($property === 'selectedPeriod') {
            $this->refreshPreview();
            return;
        }
        if ($property === 'annualYear' || str_starts_with($property, 'annual')) {
            $this->refreshAnnualPreview();
            return;
        }
        if (str_starts_with($property, 'filter') || str_starts_with($property, 'range')) {
            $this->refreshPreview();
            return;
        }
    }

    public function refreshPreview(): void
    {
        $yearMonth = $this->selectedPeriod;
        $this->previewPeriodLabel = formatPeriodLabel($yearMonth);

        $range = getPeriodRange($yearMonth);
        $this->previewPeriodStart = $range['start']->format('d/m/Y');
        $this->previewPeriodEnd = $range['end']->format('d/m/Y');

        $expenses = $this->buildBaseQuery($yearMonth)->get();

        $this->previewTotal = $expenses->sum('amount');
        $this->previewCount = $expenses->count();

        $closure = MonthlyClosure::where('month', $yearMonth)->first();
        $this->previewGains = $closure?->gains ?? 0;
        $this->previewBalance = $closure?->balance ?? ($this->previewGains - $this->previewTotal);

        $this->previewByCategory = $expenses->groupBy(fn($e) => $e->category?->parent?->translated_name ?? $e->category?->translated_name ?? __("categories.{$e->category_key}"))
            ->map(fn($items, $cat) => [
                'name' => $cat,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'percentage' => $this->previewTotal > 0 ? round(($items->sum('amount') / $this->previewTotal) * 100, 1) : 0,
            ])
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    private function buildBaseQuery(string $period): \Illuminate\Database\Eloquent\Builder
    {
        $query = Expense::with('category.parent')->byPeriod($period);

        if ($this->filterCategory) {
            $ids = ExpenseCategory::where('id', $this->filterCategory)
                ->orWhere('parent_id', $this->filterCategory)
                ->pluck('id');
            $query->whereIn('category_id', $ids);
        }

        if ($this->filterEmployee) {
            $query->where('employee_id', $this->filterEmployee);
        }

        if ($this->filterPaymentMethod) {
            $query->where('payment_method', $this->filterPaymentMethod);
        }

        return $query;
    }

    /**
     * FIX #7 : preview du rapport ANNUEL (total, 12 mois, top categories) —
     * le volet annuel téléchargeait sans jamais montrer son contenu.
     */
    public function refreshAnnualPreview(): void
    {
        $year = (int) $this->annualYear;

        $expenses = $this->buildAnnualQuery($year)->get();
        $this->annualPreviewTotal = (float) $expenses->sum('amount');
        $this->annualPreviewCount = $expenses->count();
        $activeMonths = collect(range(1, 12))->filter(function ($m) use ($expenses, $year) {
            return $expenses->contains(fn ($e) => getPeriodFromDate($e->date) === sprintf('%04d-%02d', $year, $m));
        })->count();
        $this->annualPreviewAvg = $activeMonths > 0 ? round($this->annualPreviewTotal / $activeMonths, 2) : 0;

        // 12 périodes métier (label 21→20) de l'année
        $periodTotals = [];
        foreach ($expenses as $e) {
            $p = getPeriodFromDate($e->date);
            $periodTotals[$p] = ($periodTotals[$p] ?? 0) + (float) $e->amount;
        }
        $this->annualPreviewByMonth = collect(range(1, 12))->map(function ($m) use ($year, $periodTotals) {
            $p = sprintf('%04d-%02d', $year, $m);
            $periodDate = \Carbon\Carbon::createFromFormat('Y-m', $p);
            return [
                'label' => __($periodDate->startOfMonth()->format('F')),
                'total' => (float) ($periodTotals[$p] ?? 0),
            ];
        })->toArray();

        // Top catégories
        $this->annualPreviewByCategory = \App\Domains\Reports\Support\ReportAggregator::byCategory($expenses, $this->annualPreviewTotal)
            ->map(fn ($items, $cat) => ['name' => $cat] + $items)
            ->take(6)
            ->values()
            ->toArray();
    }

    private function buildAnnualQuery(int $year): \Illuminate\Database\Eloquent\Builder
    {
        $query = Expense::with('category.parent')->byYear($year);

        $filters = [
            'category_id' => $this->annualCategory,
            'employee_id' => $this->annualEmployee,
            'payment_method' => $this->annualPaymentMethod,
        ];
        \App\Domains\Reports\Support\ReportAggregator::applyFilters($query, $filters);
        return $query;
    }

    /**
     * FIX #8 : envoi WhatsApp du PDF — le téléphone de l'utilisateur vient de
     * son profil (whatsapp_phone), le worker push le document.
     */
    public function sendWhatsApp(string $kind): void
    {
        $data = [];
        if ($kind === 'monthly') {
            $data['year'] = (int) \Carbon\Carbon::createFromFormat('!Y-m', $this->selectedPeriod)->year;
            $data['month'] = (int) \Carbon\Carbon::createFromFormat('!Y-m', $this->selectedPeriod)->month;
            $data += ['category_id' => $this->filterCategory, 'employee_id' => $this->filterEmployee, 'payment_method' => $this->filterPaymentMethod];
        } elseif ($kind === 'annual') {
            $data['year'] = $this->annualYear;
            $data += ['category_id' => $this->annualCategory, 'employee_id' => $this->annualEmployee, 'payment_method' => $this->annualPaymentMethod];
        } else {
            // Rapport personnalisé : validation préalable client
            if (!$this->rangeFrom || !$this->rangeTo) {
                $this->dispatch('toast', type: 'warning', message: __('reports.custom_range_missing'));
                return;
            }
            if ($this->rangeTo < $this->rangeFrom) {
                $this->dispatch('toast', type: 'warning', message: __('reports.custom_range_bad'));
                return;
            }
            $data = [
                'date_from' => $this->rangeFrom,
                'date_to' => $this->rangeTo,
                'category_id' => $this->filterCategory,
                'employee_id' => $this->filterEmployee,
                'payment_method' => $this->filterPaymentMethod,
            ];
        }

        $result = \App\Domains\Reports\Support\ReportPdf::shareWhatsApp(auth()->id(), $kind, $data);
        if ($result['ok']) {
            $this->notify(__('reports.share_sent'), 'success');
        } else {
            $this->notify($result['error'] ?? __('reports.share_failed'), 'error');
        }
    }

    public function render()
    {
        $years = range(now()->year, now()->year - 5);

        $earliestExpense = Expense::orderBy('date')->value('date');
        $earliestClosure = MonthlyClosure::orderBy('month')->value('month');
        $startPeriod = $earliestClosure
            ? $earliestClosure
            : ($earliestExpense ? getPeriodFromDate($earliestExpense) : now()->format('Y-m'));
        $nowPeriod = getPeriodFromDate(now());
        $allPeriods = [];
        $cursor = \Carbon\Carbon::createFromFormat('!Y-m', $nowPeriod)->startOfMonth();
        $end = \Carbon\Carbon::createFromFormat('!Y-m', $startPeriod)->startOfMonth();
        while ($cursor->greaterThanOrEqualTo($end)) {
            $p = $cursor->format('Y-m');
            $allPeriods[$p] = formatPeriodLabel($p);
            $cursor->subMonth();
        }

        $categories = ExpenseCategory::active()->with('children')->whereNull('parent_id')->orderBy('name_ar')->get();
        $employees = Employee::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('livewire.reports-index', [
            'years' => $years,
            'periods' => $allPeriods,
            'month' => (int) \Carbon\Carbon::createFromFormat('!Y-m', $this->selectedPeriod)->month,
            'year' => (int) \Carbon\Carbon::createFromFormat('!Y-m', $this->selectedPeriod)->year,
            'categories' => $categories,
            'employees' => $employees,
            'paymentMethods' => PaymentMethod::cases(),
        ])->layout('layouts.app')->title(__('nav.reports'));
    }
}
