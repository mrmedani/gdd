<?php

namespace App\Domains\Dashboard\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Domains\Alerts\Models\Alert;

/**
 * Dashboard spécialisé pour les rôles sans permissions financières (ex : Commercial) :
 * stats de contrats (les siens) + widget échéances contrats + alertes. Aucun chiffre
 * financier. Le Dashboard principal bascule automatiquement vers celui-ci via
 * launchCommercial ? voir Dashboard::mount() — ou directement par la route selon
 * le rôle (voir routes/domains/dashboard.php).
 */
class DashboardCommercial extends Component
{
    use WithPagination;

    public array $contractStats = [];
    public array $expiringContracts = [];
    public int $unreadAlerts = 0;
    public bool $showAlertsModal = false;
    public string $alertFilterType = '';
    public string $alertFilterSeverity = '';
    public string $greeting = '';
    public string $greetingIcon = '';
    public string $greetingGradient = '';
    public string $roleLabel = '';

    protected $queryString = ['alertFilterType', 'alertFilterSeverity'];

    public function mount(): void
    {
        if (!auth()->user()?->hasPermission('dashboard')) {
            abort(403);
        }

        $this->commercialModeGuard();
        $this->buildGreeting();
        $this->loadUnreadCount();
        $this->loadStats();
    }

    private function commercialModeGuard(): void
    {
        // réservé aux rôles SANS permission financière ; l'admin va sur le dashboard normal
        $financialPermissions = ['expenses', 'incomes', 'investments', 'treasury', 'reports', 'statistics', 'employees'];
        if (collect($financialPermissions)->some(fn ($p) => auth()->user()->hasPermission($p))) {
            $this->redirect(route('dashboard'), navigate: false);
        }
    }

    private function buildGreeting(): void
    {
        $hour = (int) now()->format('H');
        if ($hour < 12) {
            $this->greeting = __('dashboard.greeting_morning');
            $this->greetingIcon = 'sun';
            $this->greetingGradient = 'from-amber-400 to-orange-500';
        } elseif ($hour < 17) {
            $this->greeting = __('dashboard.greeting_afternoon');
            $this->greetingIcon = 'cloud-sun';
            $this->greetingGradient = 'from-sky-400 to-blue-600';
        } elseif ($hour < 21) {
            $this->greeting = __('dashboard.greeting_evening');
            $this->greetingIcon = 'sunset';
            $this->greetingGradient = 'from-rose-400 to-red-600';
        } else {
            $this->greeting = __('dashboard.greeting_night');
            $this->greetingIcon = 'moon';
            $this->greetingGradient = 'from-indigo-500 to-violet-600';
        }

        if ($role = auth()->user()->role) {
            $this->roleLabel = match (app()->getLocale()) {
                'ar' => $role->label_ar ?: $role->label_fr ?: $role->name,
                'fr' => $role->label_fr ?: $role->label_ar ?: $role->name,
                default => $role->name ?: $role->label_fr,
            };
        }
    }

    public function loadUnreadCount(): void
    {
        $query = Alert::unread();
        if ($prefs = auth()->user()?->alert_preferences) {
            $query->whereIn('type', $prefs);
        }
        $this->unreadAlerts = $query->count();
    }

    private function loadStats(): void
    {
        $uid = (int) auth()->id();
        $now = now()->startOfDay();
        $query = \App\Domains\Contracts\Models\Contract::visibleTo($uid);

        $total = (clone $query)->count();
        $expired = (clone $query)->whereDate('end_date', '<', $now)->count();
        $active = (clone $query)->whereDate('end_date', '>=', $now)->count();
        $expiringSoon = (clone $query)->whereDate('end_date', '>=', $now)
            ->get()->filter(fn ($c) => $c->isExpiringSoon())->count();

        $this->contractStats = [
            'total' => $total,
            'active' => $active,
            'expiring_soon' => $expiringSoon,
            'expired' => $expired,
        ];

        $this->expiringContracts = \App\Domains\Contracts\Models\Contract::visibleTo($uid)
            ->whereDate('end_date', '>=', $now)
            ->orderBy('end_date')
            ->get()
            ->filter(fn ($c) => $c->isExpiringSoon())
            ->take(6)
            ->map(fn ($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'party' => $c->party,
                'end_date' => $c->end_date->format('d/m/Y'),
                'days_left' => $c->daysUntilExpiry(),
                'urgent' => $c->daysUntilExpiry() <= 1,
            ])
            ->toArray();
    }

    public function getFilteredAlerts()
    {
        $query = Alert::latest();
        if ($prefs = auth()->user()?->alert_preferences) {
            $query->whereIn('type', $prefs);
        }
        if ($this->alertFilterType) {
            $query->where('type', $this->alertFilterType);
        }
        if ($this->alertFilterSeverity) {
            $query->where('severity', $this->alertFilterSeverity);
        }
        return $query->paginate(15);
    }

    public function getAlertTypesProperty(): array
    {
        return Alert::select('type')->distinct()->pluck('type')->toArray();
    }

    public function getAlertSeveritiesProperty(): array
    {
        return ['info', 'warning', 'error', 'success'];
    }

    public function filterByType(string $type): void
    {
        $this->alertFilterType = $type === '' ? '' : $type;
    }

    public function filterBySeverity(string $sev): void
    {
        $this->alertFilterSeverity = $this->alertFilterSeverity === $sev ? '' : $sev;
    }

    public function markAlertRead(int $id): void
    {
        $alert = Alert::findOrFail($id);
        $alert->update(['is_read' => true, 'read_at' => now()]);
        $this->loadUnreadCount();
    }

    public function closeCategory(): void {}

    public function render()
    {
        $this->buildGreeting();

        return view('livewire.dashboard-commercial', [
            'alertsPaginated' => $this->getFilteredAlerts(),
        ])
            ->layout('layouts.app')
            ->title(__('nav.dashboard'));
    }
}
