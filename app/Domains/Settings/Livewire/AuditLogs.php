<?php

namespace App\Domains\Settings\Livewire;

use App\Domains\Expenses\Models\AuditLog;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public function mount(): void
    {
        Gate::authorize('view-audit-logs');
    }

    public string $searchAction = '';
    public string $searchEntityType = '';
    public string $searchUser = '';
    public string $searchEntityId = '';
    public string $searchDateFrom = '';
    public string $searchDateTo = '';
    public ?AuditLog $selectedLog = null;
    public bool $showDetailModal = false;

    public function updatingSearchAction(): void { $this->resetPage(); }
    public function updatingSearchEntityType(): void { $this->resetPage(); }
    public function updatingSearchUser(): void { $this->resetPage(); }
    public function updatingSearchEntityId(): void { $this->resetPage(); }
    public function updatingSearchDateFrom(): void { $this->resetPage(); }
    public function updatingSearchDateTo(): void { $this->resetPage(); }

    public function showDetails(int $id): void
    {
        $this->selectedLog = AuditLog::with('user')->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function exportCsv()
    {
        Gate::authorize('view-audit-logs');
        $query = AuditLog::with('user')->latest();

        if ($this->searchAction && in_array($this->searchAction, ['created', 'updated', 'deleted'])) {
            $query->where('action', $this->searchAction);
        }
        if ($this->searchEntityType) {
            $query->where('entity_type', $this->searchEntityType);
        }
        if ($this->searchDateFrom) {
            $query->whereDate('created_at', '>=', $this->searchDateFrom);
        }
        if ($this->searchDateTo) {
            $query->whereDate('created_at', '<=', $this->searchDateTo);
        }
        if ($this->searchUser) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->searchUser . '%'));
        }
        if ($this->searchEntityId !== '') {
            $query->where('entity_id', $this->searchEntityId);
        }

        $logs = $query->get();

        $fileName = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';
        $tempPath = storage_path('app/temp/' . $fileName);

        $dir = dirname($tempPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $handle = fopen($tempPath, 'w');
        fputs($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            __('audit.user'),
            __('audit.action'),
            __('audit.entity'),
            __('audit.entity_id'),
            __('audit.date'),
            __('audit.old_values'),
            __('audit.new_values'),
        ]);

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->user?->name ?? '-',
                __('audit.' . $log->action),
                $log->entity_type,
                $log->entity_id,
                $log->created_at->format('Y-m-d H:i'),
                $log->old_values ? json_encode($log->old_values) : '',
                $log->new_values ? json_encode($log->new_values) : '',
            ]);
        }

        fclose($handle);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $query = AuditLog::with('user')->latest();

        if ($this->searchAction && in_array($this->searchAction, ['created', 'updated', 'deleted'])) {
            $query->where('action', $this->searchAction);
        }
        if ($this->searchEntityType) {
            $query->where('entity_type', $this->searchEntityType);
        }
        if ($this->searchDateFrom) {
            $query->whereDate('created_at', '>=', $this->searchDateFrom);
        }
        if ($this->searchDateTo) {
            $query->whereDate('created_at', '<=', $this->searchDateTo);
        }
        if ($this->searchUser) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->searchUser . '%'));
        }
        if ($this->searchEntityId !== '') {
            $query->where('entity_id', $this->searchEntityId);
        }

        $entityTypes = AuditLog::distinct()->pluck('entity_type');

        return view('livewire.audit-logs', [
            'logs' => $query->paginate(20),
            'entityTypes' => $entityTypes,
        ])->layout('layouts.app')->title(__('settings.audit_logs'));
    }
}
