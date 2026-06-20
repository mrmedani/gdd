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
    }

    public function closeMonthSubmit()
    {
        if (is_string($this->closeGains)) {
            $this->closeGains = str_replace(',', '.', $this->closeGains);
        }

        $this->validate([
            'closeMonth' => 'required|date_format:Y-m|before_or_equal:' . now()->format('Y-m'),
            'closeGains' => 'required|numeric|min:0',
        ]);

        if (MonthlyClosure::where('month', $this->closeMonth)->exists()) {
            $this->notify(__('caisse.already_closed', ['default' => 'Ce mois est déjà clôturé.']));
            return;
        }

        $this->calculateExpensesForMonth();
        
        $balance = (float) $this->closeGains - (float) $this->calculatedExpenses;

        MonthlyClosure::create([
            'month' => $this->closeMonth,
            'gains' => $this->closeGains,
            'expenses' => $this->calculatedExpenses,
            'balance' => $balance,
            'closed_by' => auth()->id(),
        ]);

        $this->showCloseModal = false;
        $this->reset('closeGains');

        try {
            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
                ->orWhere('notify_whatsapp', true)
                ->get();
            $closure = MonthlyClosure::where('month', $this->closeMonth)->latest()->first();
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

        return view('livewire.treasury-index', [
            'closures' => $closures,
            'globalBalance' => $globalBalance,
            'totalGains' => $totalGains,
            'totalExpenses' => $totalExpenses,
            'currentMonthClosed' => $currentMonthClosed,
        ])->layout('layouts.app')->title('Caisse');
    }
}
