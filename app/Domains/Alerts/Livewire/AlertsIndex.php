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
    public string $recurrence = 'monthly';
    public string $once_date = '';
    public string $amount = '';
    public string $lead_days = '3';
    public bool $is_active = true;
    public string $notes = '';

    public function rules(): array
    {
        $rules = [
            'label' => 'required|string|max:255',
            'recurrence' => 'required|in:monthly,once',
            'amount' => 'nullable|numeric|min:0',
            'lead_days' => 'required|integer|min:0|max:30',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:2000',
        ];
        if ($this->recurrence === 'monthly') {
            $rules['day'] = 'required|integer|min:1|max:31';
        } else {
            // échéance unique : date obligatoire, >= aujourd'hui (passée = inutile)
            $rules['once_date'] = 'required|date|after_or_equal:today';
            $rules['day'] = 'nullable';
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'recurrence.in' => __('alerts.recurrence_invalid', ['default' => "Mode d'échéance invalide."]),
            'once_date.required' => __('alerts.once_date_required', ['default' => "La date de l'échéance unique est obligatoire."]),
            'once_date.date' => __('alerts.once_date_required', ['default' => "La date de l'échéance unique est obligatoire."]),
            'once_date.after_or_equal' => __('alerts.once_date_past', ['default' => "La date doit être aujourd'hui ou plus tard."]),
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        // Un utilisateur ne peut éditer que SES engagements
        $c = Commitment::where('created_by', auth()->id())->findOrFail($id);
        $this->commitmentId = $c->id;
        $this->label = $c->label;
        $this->day = (string) $c->day;
        $this->recurrence = $c->recurrence ?? 'monthly';
        $this->once_date = $c->once_date ? $c->once_date->format('Y-m-d') : '';
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
            'recurrence' => $this->recurrence,
            'once_date' => $this->recurrence === 'once' ? $this->once_date : null,
            // jour du mois : en mode once on garde celui de la date (compat affichage)
            'day' => (int) ($this->recurrence === 'once'
                ? (\Carbon\Carbon::parse($this->once_date)->day)
                : $this->day),
            'amount' => $this->amount !== '' ? str_replace(',', '.', $this->amount) : null,
            'lead_days' => (int) $this->lead_days,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
            'created_by' => auth()->id(),
        ];

        if ($this->commitmentId) {
            Commitment::where('created_by', auth()->id())->findOrFail($this->commitmentId)->update($data);
            $this->notify(__('common.updated'));
        } else {
            Commitment::create($data);
            $this->notify(__('common.created'));
        }

        $this->resetForm();
    }

    /**
     * Marque l'engagement comme PAYÉ ce mois en créant AUTOMATIQUEMENT la dépense
     * correspondante dans /expenses (le badge ✓ Payé devient vrai par construction).
     */
    public function markAsPaid(int $id): void
    {
        $c = Commitment::where('created_by', auth()->id())->findOrFail($id);

        // Déjà payé ce mois ? (garde-fou anti-doublon)
        if ($c->wasPaidThisMonth()) {
            $this->dispatch('toast', type: 'info', message: __('alerts.already_paid_this_month'));
            return;
        }

        if ((float) $c->amount <= 0) {
            $this->dispatch('toast', type: 'warning', message: __('alerts.no_amount_for_expense'));
            return;
        }

        // Catégorie : meilleure correspondance par mots du libellé, sinon 'other', sinon 1ère catégorie active
        $tokens = array_values(array_filter(explode(' ', strtolower(trim($c->label))), fn ($t) => mb_strlen($t) >= 4));
        $categoryId = null;
        $categoryKey = null;
        $categories = \App\Domains\Expenses\Models\ExpenseCategory::query()->orderBy('id')->get();
        foreach ($categories as $cat) {
            $name = strtolower(trim((string) $cat->translated_name));
            foreach ($tokens as $t) {
                if ($t !== '' && str_contains($name, $t)) {
                    $categoryId = $cat->id;
                    $categoryKey = $cat->key;
                    break 2;
                }
            }
        }
        if ($categoryId === null) {
            $other = $categories->firstWhere('key', 'other') ?? $categories->first();
            $categoryId = $other?->id;
            $categoryKey = $other?->key;
        }

        \App\Domains\Expenses\Models\Expense::create([
            'date' => now()->toDateString(),
            'amount' => $c->amount,
            'category_id' => $categoryId,
            'category_key' => $categoryKey,
            'description' => $c->label, // description = libellé exact → la détection ✓ Payé matche par construction
            'payment_method' => 'cash',
            'notes' => 'Auto depuis /alerts (engagement #' . $c->id . ')',
            'created_by' => auth()->id(),
        ]);

        // L'observeur Expense genere la notification WhatsApp + les invalidations de caches.
        $this->dispatch('toast', type: 'success', message: __('alerts.marked_as_paid'));
    }

    public function delete(int $id): void
    {
        // Suppression : uniquement SES engagements
        Commitment::where('created_by', auth()->id())->findOrFail($id)->delete();
        $this->notify(__('common.deleted'));
    }

    public function toggleActive(int $id): void
    {
        $c = Commitment::where('created_by', auth()->id())->findOrFail($id);
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
        // Chaque utilisateur ne voit QUE les engagements qu'il a créés lui-même
        $commitments = Commitment::visibleTo(auth()->id())->orderBy('day')->paginate(15);

        $upcoming = Commitment::visibleTo(auth()->id())->where('is_active', true)->get()
            ->filter(fn ($c) => $c->isDueSoon())
            ->sortBy(fn ($c) => $c->daysUntilDue())
            ->values();

        return view('livewire.alerts-index', [
            'commitments' => $commitments,
            'upcoming' => $upcoming,
        ])->layout('layouts.app')->title(__('alerts.title'));
    }
}
