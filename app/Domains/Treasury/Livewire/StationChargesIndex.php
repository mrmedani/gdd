<?php

namespace App\Domains\Treasury\Livewire;

use App\Domains\Treasury\Models\StationCharge;
use App\Shared\Livewire\WithToast;
use Livewire\Component;

class StationChargesIndex extends Component
{
    use WithToast;

    public bool $showForm = false;
    public ?int $chargeId = null;

    public string $station_name = '';
    public string $period;
    public string $amount = '';
    public string $notes = '';

    public function mount()
    {
        $this->period = getPeriodFromDate(now()); // période courante
    }

    public function rules(): array
    {
        return [
            'station_name' => 'required|string|max:255',
            'period' => 'required|date_format:Y-m',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'station_name.required' => __('station_charges.station_required', ['default' => 'Le nom de la station est obligatoire.']),
            'amount.required' => __('validation.amount_required', ['default' => 'Le montant est obligatoire.']),
            'amount.numeric' => __('validation.amount_required', ['default' => 'Le montant est obligatoire.']),
            'amount.min' => __('validation.amount_min', ['default' => 'Le montant doit être supérieur à zéro.']),
            'period.date_format' => __('station_charges.period_invalid', ['default' => 'Période invalide.']),
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        // pré-remplir la période courante du registre
        $this->period = getPeriodFromDate(now());
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $c = StationCharge::findOrFail($id);
        $this->chargeId = $c->id;
        $this->station_name = $c->station_name;
        $this->period = $c->period;
        $this->amount = $c->amount !== null ? (string) $c->amount : '';
        $this->notes = $c->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        // Montant tolérant : espaces insécables, virgules, séparateurs de milliers
        if (is_string($this->amount)) {
            $this->amount = trim(preg_replace('/[^\d.\-]/', '', str_replace(',', '.', preg_replace('/[\s\x{00A0}\x{202F}]/u', '', $this->amount))) ?? '');
        }

        $this->validate();

        // unicité station + période (ignore soi-même en édition)
        $dup = StationCharge::where('station_name', trim($this->station_name))
            ->where('period', $this->period)
            ->when($this->chargeId, fn ($q) => $q->where('id', '!=', $this->chargeId))
            ->exists();
        if ($dup) {
            $this->addError('station_name', __('station_charges.duplicate', [
                'name' => $this->station_name,
                'default' => 'Une commission pour cette station existe déjà pour cette période.',
            ]));
            return;
        }

        $data = [
            'station_name' => trim($this->station_name),
            'period' => $this->period,
            'amount' => $this->amount,
            'notes' => trim($this->notes) !== '' ? mb_substr(trim($this->notes), 0, 2000) : null,
            'created_by' => auth()->id(),
        ];

        if ($this->chargeId) {
            StationCharge::where('id', $this->chargeId)->update($data);
            $this->notify(__('station_charges.updated', ['default' => 'Commission mise à jour.']));
        } else {
            StationCharge::create($data);
            $this->notify(__('station_charges.saved', ['default' => 'Commission enregistrée.']));
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        StationCharge::findOrFail($id)->delete();
        $this->notify(__('station_charges.deleted', ['default' => 'Commission supprimée.']));
    }

    public function prevPeriod(): void
    {
        $this->period = \Carbon\Carbon::createFromFormat('Y-m', $this->period)
            ->subMonthNoOverflow()->format('Y-m');
    }

    public function nextPeriod(): void
    {
        $this->period = \Carbon\Carbon::createFromFormat('Y-m', $this->period)
            ->addMonthNoOverflow()->format('Y-m');
    }

    public function resetForm(): void
    {
        $this->reset(['showForm', 'chargeId', 'station_name', 'period', 'amount', 'notes']);
    }

    public function render()
    {
        $charges = StationCharge::period($this->period)->get();
        $total = StationCharge::totalFor($this->period);

        return view('livewire.station-charges', [
            'charges' => $charges,
            'total' => $total,
            'knownStations' => StationCharge::knownStations(),
            'periodLabel' => formatPeriodLabel($this->period),
            'currentPeriod' => getPeriodFromDate(now()),
            'isFuture' => \Carbon\Carbon::createFromFormat('Y-m', $this->period)
                ->gt(\Carbon\Carbon::createFromFormat('Y-m', getPeriodFromDate(now()))),
            'clotureRange' => getPeriodRange($this->period),
        ]);
    }
}
