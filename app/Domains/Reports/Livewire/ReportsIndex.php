<?php

namespace App\Domains\Reports\Livewire;

use App\Domains\Expenses\Models\Expense;
use App\Domains\Treasury\Models\MonthlyClosure;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ReportsIndex extends Component
{
    public string $selectedPeriod = '';
    public int $annualYear;

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
    }

    public function updatedSelectedPeriod(): void
    {
        $this->refreshPreview();
    }

    public function updatedAnnualYear(): void
    {
        $this->refreshPreview();
    }

    public function refreshPreview(): void
    {
        $yearMonth = $this->selectedPeriod;
        $this->previewPeriodLabel = formatPeriodLabel($yearMonth);

        $range = getPeriodRange($yearMonth);
        $this->previewPeriodStart = $range['start']->format('d/m/Y');
        $this->previewPeriodEnd = $range['end']->format('d/m/Y');

        $expenses = Expense::with('category')
            ->byPeriod($yearMonth)
            ->latest('date')
            ->get();

        $this->previewTotal = $expenses->sum('amount');
        $this->previewCount = $expenses->count();

        $closure = MonthlyClosure::where('month', $yearMonth)->first();
        $this->previewGains = $closure?->gains ?? 0;
        $this->previewBalance = $closure?->balance ?? ($this->previewGains - $this->previewTotal);

        $this->previewByCategory = $expenses->groupBy(fn($e) => $e->category?->translated_name ?? __("categories.{$e->category_key}"))
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

        return view('livewire.reports-index', [
            'years' => $years,
            'periods' => $allPeriods,
            'month' => (int) \Carbon\Carbon::createFromFormat('!Y-m', $this->selectedPeriod)->month,
            'year' => (int) \Carbon\Carbon::createFromFormat('!Y-m', $this->selectedPeriod)->year,
        ])->layout('layouts.app')->title(__('nav.reports'));
    }
}
