<?php

namespace App\Domains\Treasury\Livewire;

use App\Domains\Treasury\Models\Investment;
use App\Shared\Livewire\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class InvestmentsIndex extends Component
{
    use WithPagination, WithToast;

    public bool $showForm = false;
    public ?int $investmentId = null;

    public string $date = '';
    public string $amount = '';
    public string $type = 'equipment';
    public string $beneficiary = '';
    public string $notes = '';

    public string $period = '';
    public string $typeFilter = '';

    public float $totalInvestments = 0;

    public const TYPES = [
        'equipment' => 'investments.type_equipment',
        'project' => 'investments.type_project',
        'other' => 'investments.type_other',
    ];

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:equipment,project,other',
            'beneficiary' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function mount(): void
    {
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
        $investment = Investment::findOrFail($id);
        $this->investmentId = $investment->id;
        $this->date = $investment->date->format('Y-m-d');
        $this->amount = (string) $investment->amount;
        $this->type = $investment->type;
        $this->beneficiary = $investment->beneficiary ?? '';
        $this->notes = $investment->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'date' => $this->date,
            'amount' => str_replace(',', '.', $this->amount),
            'type' => $this->type,
            'beneficiary' => $this->beneficiary ?: null,
            'notes' => $this->notes ?: null,
            'created_by' => auth()->id(),
        ];

        if ($this->investmentId) {
            Investment::findOrFail($this->investmentId)->update($data);
            $this->notify(__('common.updated'));
        } else {
            Investment::create($data);
            $this->notify(__('common.created'));
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $investment = Investment::findOrFail($id);
        $investment->delete();
        $this->notify(__('common.deleted'));
    }

    public function resetForm(): void
    {
        $this->reset(['investmentId', 'amount', 'beneficiary', 'notes']);
        $this->type = 'equipment';
        $this->date = now()->format('Y-m-d');
        $this->showForm = false;
        $this->resetValidation();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function filterByType(string $type): void
    {
        $this->typeFilter = $this->typeFilter === $type ? '' : $type;
        $this->resetPage();
    }

    public function render()
    {
        $range = getPeriodRange($this->period);

        $query = Investment::query()
            ->whereBetween('date', [$range['start'], $range['end']])
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->latest('date');

        $this->totalInvestments = (float) Investment::whereBetween('date', [$range['start'], $range['end']])
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->sum('amount');

        $totalsByType = Investment::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $typeCards = collect(self::TYPES)->map(function ($labelKey, $key) use ($totalsByType) {
            $row = $totalsByType->get($key);
            return [
                'key' => $key,
                'label' => __($labelKey),
                'total' => $row ? (float) $row->total : 0,
                'count' => $row ? (int) $row->count : 0,
            ];
        })->values()->toArray();

        $investments = $query->paginate(15);

        return view('livewire.investments-index', [
            'investments' => $investments,
            'typeCards' => $typeCards,
        ])->layout('layouts.app')->title(__('investments.title'));
    }
}
