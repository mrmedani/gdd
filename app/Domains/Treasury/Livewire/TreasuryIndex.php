<?php

namespace App\Domains\Treasury\Livewire;

use App\Domains\Alerts\Notifications\MonthlyClosureNotification;
use App\Domains\Expenses\Models\Expense;
use App\Domains\Treasury\Models\MonthlyClosure;
use App\Models\User;
use App\Shared\Livewire\WithToast;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class TreasuryIndex extends Component
{
    use WithPagination, WithToast;

    public $showCloseModal = false;
    public $showDeleteModal = false;
    public $deleteClosureId = null;
    public string $deletePassword = '';

    // Close Month Form
    public string $closeMonth = '';
    public string $closeGains = '';
    public string $calculatedExpenses = '0';
    public string $calculatedIncomes = '0';
    public string $calculatedInvestments = '0';
    public string $closeNote = '';

    // Inline note editing (tableau historique)
    public ?int $editingNoteId = null;
    public string $editingNote = '';

    public function mount()
    {
        Gate::authorize('manage-treasury');
        $this->closeMonth = getPeriodFromDate(now());
        $this->calculateExpensesForMonth();
    }

    public function updatedCloseMonth()
    {
        $this->calculateExpensesForMonth();
    }

    public function calculateExpensesForMonth()
    {
        if (!$this->closeMonth) return;

        $range = getPeriodRange($this->closeMonth);

        $this->calculatedExpenses = Expense::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
        $this->calculatedIncomes = \App\Domains\Treasury\Models\Income::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
        $this->calculatedInvestments = \App\Domains\Treasury\Models\Investment::whereBetween('date', [$range['start'], $range['end']])->sum('amount');
    }

    public function closeMonthSubmit()
    {
        if (is_string($this->closeGains)) {
            // Normalisation robuste : espaces ASCII, insécables (U+00A0), étroits (U+202F),
            // virgules décimales et séparateurs de milliers — tout est retiré sauf chiffres, . et -
            $this->closeGains = trim(preg_replace('/[^\d.\-]/', '', str_replace(',', '.', preg_replace('/[\s\x{00A0}\x{202F}]/u', '', $this->closeGains))) ?? '');
        }

        $this->validate([
            'closeMonth' => 'required|date_format:Y-m|before_or_equal:' . now()->format('Y-m'),
            'closeGains' => 'required|numeric|min:0',
        ], [
            'closeGains.required' => __('validation.amount_required'),
            'closeGains.numeric' => __('validation.amount_required'),
            'closeGains.min' => __('validation.amount_min'),
        ]);

        if (MonthlyClosure::where('month', $this->closeMonth)->exists()) {
            $this->notify(__('caisse.already_closed', ['default' => 'Ce mois est déjà clôturé.']));
            return;
        }

        $this->calculateExpensesForMonth();
        
        $balance = (float) $this->closeGains + (float) $this->calculatedIncomes - (float) $this->calculatedExpenses - (float) $this->calculatedInvestments;

        try {
            MonthlyClosure::create([
                'month' => $this->closeMonth,
                'gains' => $this->closeGains,
                'expenses' => $this->calculatedExpenses,
                'investments' => $this->calculatedInvestments,
                'balance' => $balance,
                'closed_by' => auth()->id(),
                'closure_note' => trim($this->closeNote) !== '' ? mb_substr(trim($this->closeNote), 0, 2000) : null,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->notify(__('caisse.already_closed', ['default' => 'Ce mois est déjà clôturé.']));
                return;
            }
            throw $e;
        }

        $this->showCloseModal = false;
        $this->reset('closeGains');

        try {
            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            $closure = MonthlyClosure::where('month', $this->closeMonth)->first();
            if ($closure) {
                Notification::sendNow($admins, new MonthlyClosureNotification($closure));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send closure notification: ' . $e->getMessage());
        }

        $this->notify(__('common.saved'));
    }

    public function confirmDelete(int $id)
    {
        Gate::authorize('manage-delete-closure');
        $this->deleteClosureId = $id;
        $this->deletePassword = '';
        $this->showDeleteModal = true;
    }

    /** Éditer une note de clôture existante (in-place). */
    public function startEditNote(int $id)
    {
        Gate::authorize('manage-treasury');
        $closure = MonthlyClosure::findOrFail($id);
        $this->editingNoteId = $closure->id;
        $this->editingNote = (string) ($closure->closure_note ?? '');
        $this->resetValidation('editingNote');
    }

    public function saveNote(): void
    {
        Gate::authorize('manage-treasury');
        $this->validate([
            'editingNote' => 'nullable|string|max:2000',
        ]);
        $c = MonthlyClosure::findOrFail($this->editingNoteId);
        $c->update([
            'closure_note' => trim($this->editingNote) !== '' ? mb_substr(trim($this->editingNote), 0, 2000) : null,
        ]);
        $this->editingNoteId = null;
        $this->editingNote = '';
        $this->notify(__('caisse.note_saved', ['default' => 'Note enregistrée.']));
    }

    public function cancelEditNote(): void
    {
        $this->editingNoteId = null;
        $this->editingNote = '';
    }

    public function deleteClosure()
    {
        Gate::authorize('manage-delete-closure');

        $this->validate([
            'deletePassword' => 'required',
        ]);

        if (!Hash::check($this->deletePassword, auth()->user()->password)) {
            $this->addError('deletePassword', __('caisse.delete_wrong_password'));
            return;
        }

        $closure = MonthlyClosure::findOrFail($this->deleteClosureId);
        $closure->delete();

        $this->showDeleteModal = false;
        $this->deleteClosureId = null;
        $this->deletePassword = '';

        $this->notify(__('caisse.delete_success'));
    }

    public function render()
    {
        $closures = MonthlyClosure::with('closer')->orderBy('month', 'desc')->paginate(12);
        $globalBalance = MonthlyClosure::sum('balance');
        $totalGains = MonthlyClosure::sum('gains');
        $totalExpenses = MonthlyClosure::sum('expenses');
        $currentMonthClosed = MonthlyClosure::where('month', getPeriodFromDate(now()))->exists();
        $closedMonths = MonthlyClosure::pluck('month')->toArray();

        // Calcul du taux de croissance mois par mois
        $allClosures = MonthlyClosure::orderBy('month', 'asc')->get(['month', 'gains']);
        $growthRates = [];
        $prevGains = null;
        foreach ($allClosures as $c) {
            if ($prevGains !== null && $prevGains > 0) {
                $growthRates[$c->month] = round((($c->gains - $prevGains) / $prevGains) * 100, 1);
            } else {
                $growthRates[$c->month] = null;
            }
            $prevGains = $c->gains;
        }

        return view('livewire.treasury-index', [
            'closures' => $closures,
            'globalBalance' => $globalBalance,
            'totalGains' => $totalGains,
            'totalExpenses' => $totalExpenses,
            'currentMonthClosed' => $currentMonthClosed,
            'closedMonths' => $closedMonths,
            'growthRates' => $growthRates,
        ])->layout('layouts.app')->title('Caisse');
    }
}
