<?php

namespace App\Domains\Treasury\Livewire;

use App\Domains\Treasury\Models\Income;
use App\Models\User;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class IncomesIndex extends Component
{
    use WithPagination, WithToast;

    public bool $showForm = false;
    public ?int $incomeId = null;

    public string $date = '';
    public string $amount = '';
    public string $source_type = 'investment';
    public string $sub_type = '';
    public string $source_name = '';
    public string $notes = '';

    public string $period = '';
    public string $sourceFilter = '';

    public float $totalIncomes = 0;

    public const SOURCE_TYPES = [
        'investment' => 'Investissement',
        'franchise_fee' => 'Droits de franchise',
        'other' => 'Autre',
    ];

    public const SUB_TYPES = [
        'individual' => 'Investisseur (personne physique)',
        'company' => 'Investisseur (personne morale / entreprise)',
    ];

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'source_type' => 'required|in:investment,franchise_fee,other',
            'sub_type' => 'nullable|in:individual,company',
            'source_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('manage-incomes');
        $this->period = getPeriodFromDate(now());
        $this->date = now()->format('Y-m-d');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->date = now()->format('Y-m-d');
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $income = Income::findOrFail($id);
        $this->incomeId = $income->id;
        $this->date = $income->date->format('Y-m-d');
        $this->amount = (string) $income->amount;
        $this->source_type = $income->source_type;
        $this->sub_type = $income->sub_type ?? '';
        $this->source_name = $income->source_name ?? '';
        $this->notes = $income->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'date' => $this->date,
            'amount' => str_replace(',', '.', $this->amount),
            'source_type' => $this->source_type,
            'sub_type' => $this->source_type === 'investment' ? ($this->sub_type ?: null) : null,
            'source_name' => $this->source_name ?: null,
            'notes' => $this->notes ?: null,
            'created_by' => auth()->id(),
        ];

        if ($this->incomeId) {
            Income::findOrFail($this->incomeId)->update($data);
            $this->notify(__('common.updated'));
        } else {
            Income::create($data);
            $this->notify(__('common.created'));
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $income = Income::findOrFail($id);
        $income->delete();
        $this->notify(__('common.deleted'));
    }

    public function resetForm(): void
    {
        $this->reset(['incomeId', 'amount', 'source_name', 'sub_type', 'notes']);
        $this->source_type = 'investment';
        $this->date = now()->format('Y-m-d');
        $this->showForm = false;
        $this->resetValidation();
    }

    public function updatedSourceType(): void
    {
        // reset sub_type when source type changes away from investment
        if ($this->source_type !== 'investment') {
            $this->sub_type = '';
        }
    }

    public function updatedSourceFilter(): void
    {
        $this->resetPage();
    }

    public function filterByType(string $type): void
    {
        $this->sourceFilter = $this->sourceFilter === $type ? '' : $type;
        $this->resetPage();
    }

    public function render()
    {
        $range = getPeriodRange($this->period);

        $query = Income::query()
            ->whereBetween('date', [$range['start'], $range['end']])
            ->when($this->sourceFilter, fn ($q) => $q->where('source_type', $this->sourceFilter))
            ->latest('date');

        $this->totalIncomes = (float) Income::whereBetween('date', [$range['start'], $range['end']])
            ->when($this->sourceFilter, fn ($q) => $q->where('source_type', $this->sourceFilter))
            ->sum('amount');

        $totalsByType = Income::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('source_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('source_type')
            ->get()
            ->keyBy('source_type');

        $sourceCards = collect(self::SOURCE_TYPES)->map(function ($label, $key) use ($totalsByType) {
            $row = $totalsByType->get($key);
            return [
                'key' => $key,
                'label' => $label,
                'total' => $row ? (float) $row->total : 0,
                'count' => $row ? (int) $row->count : 0,
            ];
        })->values()->toArray();

        $incomes = $query->paginate(15);

        return view('livewire.incomes-index', [
            'incomes' => $incomes,
            'sourceCards' => $sourceCards,
        ])->layout('layouts.app')->title(__('incomes.title'));
    }
}
