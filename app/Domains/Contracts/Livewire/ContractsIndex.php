<?php

namespace App\Domains\Contracts\Livewire;

use App\Domains\Contracts\Models\Contract;
use App\Shared\Livewire\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class ContractsIndex extends Component
{
    use WithPagination, WithToast;

    public bool $showForm = false;
    public ?int $contractId = null;

    public string $title = '';
    public string $party = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $notes = '';

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'party' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        // Un utilisateur ne peut éditer que SES contrats
        $c = Contract::where('created_by', auth()->id())->findOrFail($id);
        $this->contractId = $c->id;
        $this->title = $c->title;
        $this->party = $c->party ?? '';
        $this->start_date = $c->start_date->format('Y-m-d');
        $this->end_date = $c->end_date->format('Y-m-d');
        $this->notes = $c->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'party' => $this->party ?: null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'notes' => $this->notes ?: null,
            'created_by' => auth()->id(),
        ];

        if ($this->contractId) {
            Contract::where('created_by', auth()->id())->findOrFail($this->contractId)->update($data);
            $this->notify(__('common.updated'));
        } else {
            Contract::create($data);
            $this->notify(__('common.created'));
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Contract::where('created_by', auth()->id())->findOrFail($id)->delete();
        $this->notify(__('common.deleted'));
    }

    public function resetForm(): void
    {
        $this->reset(['contractId', 'title', 'party', 'notes']);
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addYear()->format('Y-m-d');
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        // Liste personnelle : chaque gestionnaire ne voit que ses contrats
        // (même règle que /alerts — les alertes WhatsApp partent au créateur).
        $contracts = Contract::visibleTo(auth()->id())
            ->orderBy('end_date')
            ->paginate(15);

        $expiring = Contract::visibleTo(auth()->id())
            ->where('end_date', '>=', now()->startOfDay())
            ->get()
            ->filter(fn ($c) => $c->isExpiringSoon())
            ->sortBy(fn ($c) => $c->daysUntilExpiry())
            ->values();

        return view('livewire.contracts.contracts-index', [
            'contracts' => $contracts,
            'expiring' => $expiring,
        ]);
    }
}
