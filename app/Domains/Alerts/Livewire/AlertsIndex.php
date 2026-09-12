<?php

namespace App\Domains\Alerts\Livewire;

use App\Domains\Alerts\Models\Commitment;
use App\Shared\Livewire\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class AlertsIndex extends Component
{
    use WithPagination, WithToast;

    public bool $showForm = false;
    public ?int $commitmentId = null;

    public string $label = '';
    public string $day = '1';
    public string $amount = '';
    public string $lead_days = '3';
    public bool $is_active = true;
    public string $notes = '';

    public function rules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'day' => 'required|integer|min:1|max:31',
            'amount' => 'nullable|numeric|min:0',
            'lead_days' => 'required|integer|min:0|max:30',
            'is_active' => 'boolean',
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
        $c = Commitment::findOrFail($id);
        $this->commitmentId = $c->id;
        $this->label = $c->label;
        $this->day = (string) $c->day;
        $this->amount = $c->amount !== null ? (string) $c->amount : '';
        $this->lead_days = (string) $c->lead_days;
        $this->is_active = (bool) $c->is_active;
        $this->notes = $c->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'label' => $this->label,
            'day' => (int) $this->day,
            'amount' => $this->amount !== '' ? str_replace(',', '.', $this->amount) : null,
            'lead_days' => (int) $this->lead_days,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
            'created_by' => auth()->id(),
        ];

        if ($this->commitmentId) {
            Commitment::findOrFail($this->commitmentId)->update($data);
            $this->notify(__('common.updated'));
        } else {
            Commitment::create($data);
            $this->notify(__('common.created'));
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Commitment::findOrFail($id)->delete();
        $this->notify(__('common.deleted'));
    }

    public function toggleActive(int $id): void
    {
        $c = Commitment::findOrFail($id);
        $c->update(['is_active' => !$c->is_active]);
    }

    public function resetForm(): void
    {
        $this->reset(['commitmentId', 'label', 'amount', 'notes']);
        $this->day = '1';
        $this->lead_days = '3';
        $this->is_active = true;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        $commitments = Commitment::orderBy('day')->paginate(15);

        $upcoming = Commitment::where('is_active', true)->get()
            ->filter(fn ($c) => $c->isDueSoon())
            ->sortBy(fn ($c) => $c->daysUntilDue())
            ->values();

        return view('livewire.alerts-index', [
            'commitments' => $commitments,
            'upcoming' => $upcoming,
        ])->layout('layouts.app')->title(__('alerts.title'));
    }
}
