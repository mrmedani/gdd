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
    public ?string $period = null;   // nullable : Livewire reset() assigne null, jamais un TypeError
    public string $amount = '';
    public string $notes = '';

    public function mount()
    {
        $this->period = getPeriodFromDate(now()); // période courante
    }

    public function rules(): array
    {
        // Période bornée : <= période courante (une commission d'une période
        // future n'a pas de sens pour la clôture — antérieures OK, clôturées ou pas).
        return [
            'station_name' => 'required|string|max:255',
            'period' => 'required|date_format:Y-m|before_or_equal:' . getPeriodFromDate(now()),
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
            'period.before_or_equal' => __('station_charges.period_future', ['default' => 'Impossible d\'enregistrer une commission pour une période future.']),
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        // FIX : PRÉ-REMPLIT la période en cours de NAVIGATION (pas forcé « la
        // période courante ») — sinon impossible d'ajouter pour une période
        // antérieure non clôturée : le modal écrasait la période consultée.
        $this->period = $this->period ?: getPeriodFromDate(now());
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
        // FIX 500 au clic « Annuler » : reset(['period']) remettait period à null
        // (pas de valeur de défaut déclarée) → getPeriodRange(null) plantait le
        // render suivant. On réétablit toujours la période courante.
        $this->reset(['showForm', 'chargeId', 'station_name', 'amount', 'notes']);
        if (!$this->period || !\Carbon\Carbon::canBeCreatedFromFormat($this->period, 'Y-m')) {
            $this->period = getPeriodFromDate(now());
        }
    }

    public function render()
    {
        $charges = StationCharge::period($this->period)->get();
        $total = StationCharge::totalFor($this->period);

        // OPTIONS de saut rapide : la période courante + les 23 précédentes —
        // label = la vraie PERIODE platform (21→20), pas un simple mois.
        $current = getPeriodFromDate(now());
        $availablePeriods = [];
        $p = \Carbon\Carbon::createFromFormat('Y-m', $current);
        for ($i = 0; $i < 24; $i++) {
            $ym = $p->format('Y-m');
            $r = getPeriodRange($ym);
            $tag = $ym === $current ? __('station_charges.current', ['default' => 'en cours']) : '';
            $availablePeriods[$ym] = trim(__('station_charges.period_option', [
                'ym' => $ym,
                'start' => $r['start']->format('d/m/Y'),
                'end' => $r['end']->format('d/m/Y'),
                'default' => 'Période :ym · du :start au :end',
            ]) . ' ' . $tag);
            $p->subMonthNoOverflow();
        }

        return view('livewire.station-charges', [
            'charges' => $charges,
            'total' => $total,
            'knownStations' => StationCharge::knownStations(),
            'periodLabel' => formatPeriodLabel($this->period),
            'currentPeriod' => $current,
            'availablePeriods' => $availablePeriods,
            'isFuture' => \Carbon\Carbon::createFromFormat('Y-m', $this->period)
                ->gt(\Carbon\Carbon::createFromFormat('Y-m', $current)),
            'clotureRange' => getPeriodRange($this->period),
        ]);
    }
}
